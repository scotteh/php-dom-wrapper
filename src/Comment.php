<?php

namespace DOMWrap;

use DOMWrap\Traits\NodeTrait;
use DOMWrap\Traits\CommonTrait;
use DOMWrap\Traits\TraversalTrait;
use DOMWrap\Traits\ManipulationTrait;

/**
 * Comment Node
 *
 * @package DOMWrap
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class Comment extends \DOMComment
{
    use CommonTrait;
    use NodeTrait;
    use TraversalTrait;
    use ManipulationTrait;
}
