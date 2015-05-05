<?php

namespace DOMWrap;

use DOMWrap\Traits\NodeTrait;

/**
 * Comment Node
 *
 * @package DOMWrap
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class Comment extends \DOMComment
{
    use NodeTrait;
}
