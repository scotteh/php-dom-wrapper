<?php

namespace DOMWrap;

use DOMWrap\Traits\NodeTrait;
use DOMWrap\Traits\CommonTrait;
use DOMWrap\Traits\TraversalTrait;
use DOMWrap\Traits\ManipulationTrait;

/**
 * DocumentType Node
 *
 * @package DOMWrap
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class DocumentType extends \DOMDocumentType
{
    use CommonTrait;
    use NodeTrait;
    use TraversalTrait;
    use ManipulationTrait;
}
