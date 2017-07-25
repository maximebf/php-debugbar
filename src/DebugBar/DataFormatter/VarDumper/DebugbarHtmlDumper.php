<?php

namespace DebugBar\DataFormatter\VarDumper;

use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * We have to extend the base HtmlDumper class in order to get access to the protected-only
 * getDumpHeader function.
 */
class DebugBarHtmlDumper extends HtmlDumper
{
    public function getDumpHeaderByDebugBar() {
        // getDumpHeader is protected:
        return str_replace('pre.sf-dump', '.debugbar pre.sf-dump', $this->getDumpHeader());
    }
}
