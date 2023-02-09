<?php

namespace App\Libs\Utils;

/**
 * Class providing Route Names.
 *
 * The structure of the route names:
 *
 * POST_PASSWORD_RESET_CHANGE_STORE
 * | 1| |      2     | |  3 | | 4 |
 *
 * 1: Method
 *
 * 2: Group 1
 *
 * 3: Group 2
 *
 * ---etc---
 *
 * 4: Action - as in: Create / Store / Update / etc
 *
 * Not required - can be completely skipped.
 */
class NamedRoute
{
    /* -- INDEX GROUP -- */
    public const GET_INDEX = 'index';
}
