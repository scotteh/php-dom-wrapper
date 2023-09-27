<?php declare(strict_types=1);

namespace DOMWrap;

use DOMWrap\Traits\{
    CommonTrait,
    TraversalTrait,
    ManipulationTrait
};

/**
 * Document Node
 *
 * @package DOMWrap
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class Document extends \DOMDocument
{
    use CommonTrait;
    use TraversalTrait;
    use ManipulationTrait;

    /** @var int */
    protected $libxmlOptions = LIBXML_NONET | LIBXML_HTML_NODEFDTD;

    /** @var string|null */
    protected $documentEncoding = null;

    public function __construct(string $version = '1.0', string $encoding = 'UTF-8') {
        parent::__construct($version, $encoding);

        $this->registerNodeClass('DOMText', 'DOMWrap\\Text');
        $this->registerNodeClass('DOMElement', 'DOMWrap\\Element');
        $this->registerNodeClass('DOMComment', 'DOMWrap\\Comment');
        $this->registerNodeClass('DOMDocument', 'DOMWrap\\Document');
        $this->registerNodeClass('DOMDocumentType', 'DOMWrap\\DocumentType');
        $this->registerNodeClass('DOMProcessingInstruction', 'DOMWrap\\ProcessingInstruction');
    }

    /**
     * Set libxml options.
     *
     * Multiple values must use bitwise OR.
     * eg: LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
     *
     * @link http://php.net/manual/en/libxml.constants.php
     *
     * @param int $libxmlOptions
     */
    public function setLibxmlOptions(int $libxmlOptions): void {
        $this->libxmlOptions = $libxmlOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function document(): ?\DOMDocument {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function collection(): NodeList {
        return $this->newNodeList([$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function result(NodeList $nodeList): NodeList|\DOMNode|null {
        if ($nodeList->count()) {
            return $nodeList->first();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function parent(string|NodeList|\DOMNode|callable|null $selector = null): Document|Element|NodeList|null {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function parents(?string $selector = null): NodeList {
        return $this->newNodeList();
    }

    /**
     * {@inheritdoc}
     */
    public function substituteWith(string|NodeList|\DOMNode|callable $input): self {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->replaceChild($newNode, $node);
            }
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function _clone(): void {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml(bool $isIncludeAll = false): string {
        return $this->getOuterHtml($isIncludeAll);
    }

    /**
     * {@inheritdoc}
     */
    public function setHtml(string|NodeList|\DOMNode|callable $input): self {
        if (!is_string($input) || trim($input) == '') {
            return $this;
        }

        $internalErrors = libxml_use_internal_errors(true);
        if (\PHP_VERSION_ID < 80000) {
            $disableEntities = libxml_disable_entity_loader(true);
            $this->composeXmlNode($input);
            libxml_use_internal_errors($internalErrors);
            libxml_disable_entity_loader($disableEntities);
        } else {
            $this->composeXmlNode($input);
            libxml_use_internal_errors($internalErrors);
        }

        return $this;
    }

    /**
     * @param string $html
     * @param int $options
     *
     * @return bool
     */
    public function loadHTML(string $html, int $options = 0): bool {
        // Fix LibXML's crazy-ness RE root nodes
        // While importing HTML using the LIBXML_HTML_NOIMPLIED option LibXML insists
        //  on having one root node. All subsequent nodes are appended to this first node.
        // To counter this we will create a fake element, allow LibXML to 'do its thing'
        //  then undo it by taking the contents of the fake element, placing it back into
        //  the root and then remove our fake element.
        if ($options & LIBXML_HTML_NOIMPLIED) {
            $html = '<domwrap></domwrap>' . $html;
        }

        $html = '<?xml encoding="' . ($this->getEncoding() ?? 'UTF-8') . '">' . $html;

        $result = parent::loadHTML($html, $options);

        // Do our re-shuffling of nodes.
        if ($this->libxmlOptions & LIBXML_HTML_NOIMPLIED) {
            $this->children()->first()->contents()->each(function($node){
                $this->appendWith($node);
            });

            $this->removeChild($this->children()->first());
        }

        return $result;
    }

    /**
     * @param \DOMNode $node
     *
     * @return string|bool
     */
    public function saveHTML(?\DOMNode $node = null): string|false {
        $target = $node ?: $this;

        // Undo any url encoding of attributes automatically applied by LibXML.
        // See htmlAttrDumpOutput() in:
        //    https://github.com/GNOME/libxml2/blob/master/HTMLtree.c
        $i = 0;
        $search = [];
        $replace = [];
        $escapes = [
            ['attr' => 'src'],
            ['attr' => 'href'],
            ['attr' => 'action'],
            ['attr' => 'name', 'tag' => 'a'],
        ];

        $nodes = $target->find('*[src],*[href],*[action],a[name]', 'descendant-or-self::');

        foreach ($nodes as $node) {
            foreach ($escapes as $escape) {
                if (
                    (!array_key_exists('tag', $escape) || strcasecmp($node->tagName, $escape['tag']) === 0)
                    && $node->hasAttribute($escape['attr'])
                ) {
                    $value = $node->getAttribute($escape['attr']);
                    $newName = 'DOMWRAP--ATTR-' . $i . '--' . $escape['attr'];

                    $node->setAttribute($newName, $value);
                    $node->removeAttribute($escape['attr']);

                    // Determine if the attribute will be wrapped in single
                    //  or double quotes and further encodings to apply.
                    //
                    // See xmlBufWriteQuotedString() in:
                    //    https://github.com/GNOME/libxml2/blob/master/buf.c
                    $hasQuot = strstr($value, '"');
                    $hasApos = strstr($value, "'");

                    if ($hasQuot && $hasApos) {
                        $value = str_replace('"', '&quot;', $value);
                    }

                    $char = '"';

                    if ($hasQuot && !$hasApos) {
                        $char = "'";
                    }

                    // See xmlEscapeEntities() in:
                    //    https://github.com/GNOME/libxml2/blob/master/xmlsave.c
					$searchValue = str_replace(['<', '>', '&'], ['&lt;', '&gt;', '&amp;'], $value);

                    $search[] = $newName. '=' . $char . $searchValue . $char;
                    $replace[] = $escape['attr']. '=' . $char . $value . $char;

                    $i++;
                }
            }
        }

        $html = parent::saveHTML($target);

        $html = str_replace($search, $replace, $html);

        return $html;
    }

    /*
     * @param $encoding string|null
     */
    public function setEncoding(?string $encoding = null): void {
        $this->documentEncoding = $encoding;
    }

    /*
     * @return string|null
     */
    public function getEncoding(): ?string {
        return $this->documentEncoding;
    }

    /*
     * @param $html string
     *
     * @return string|null
     */
    private function getCharset(string $html): ?string {
        $charset = null;

        if (preg_match('@<meta[^>]*?charset=["\']?([^"\'\s>]+)@im', $html, $matches)) {
            $charset = mb_strtoupper($matches[1]);
        }

        return $charset;
    }

    /*
     * @param $html string
     */
    private function detectEncoding(string $html): void {
        $charset = $this->getEncoding();

        if (is_null($charset)) {
            $charset = $this->getCharset($html);
        }

        $detectedCharset = mb_detect_encoding($html, mb_detect_order(), true);

        if ($charset === null && $detectedCharset == 'UTF-8') {
            $charset = $detectedCharset;
        }

        $this->setEncoding($charset);
    }

    /*
     * @param $html string
     *
     * @return string
     */
    private function convertToUtf8(string $html): string {
        $charset = $this->getEncoding();

        if ($charset !== null) {
            $html = preg_replace('@(charset=["]?)([^"\s]+)([^"]*["]?)@im', '$1UTF-8$3', $html);
            $mbHasCharset = in_array($charset, array_map('mb_strtoupper', mb_list_encodings()));

            if ($mbHasCharset) {
                $html = mb_convert_encoding($html, 'UTF-8', $charset);

            // Fallback to iconv if available.
            } elseif (extension_loaded('iconv')) {
                $htmlIconv = iconv($charset, 'UTF-8', $html);

                if ($htmlIconv !== false) {
                    $html = $htmlIconv;
                } else {
                    $charset = null;
                }
            }
        }

        if ($charset === null) {
            $html = htmlspecialchars_decode(mb_encode_numericentity(htmlentities($html, ENT_QUOTES, 'UTF-8'), [0x80, 0x10FFFF, 0, ~0], 'UTF-8'));
        }

        return $html;
    }

    /**
     * @param $html string
     */
    private function composeXmlNode(string $html): void {
        $this->detectEncoding($html);

        $html = $this->convertToUtf8($html);

        $this->loadHTML($html, $this->libxmlOptions);

        // Remove <?xml ...> processing instruction.
        $this->contents()->each(function($node) {
            if ($node instanceof ProcessingInstruction && $node->nodeName == 'xml') {
                $node->destroy();
            }
        });
    }
}
