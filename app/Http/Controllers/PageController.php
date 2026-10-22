<?php

namespace App\Http\Controllers;

use App\Support\HtmlPageRenderer;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function home(HtmlPageRenderer $pages): Response
    {
        return $pages->response('index');
    }

    public function show(string $page, HtmlPageRenderer $pages): Response
    {
        return $pages->response($page);
    }
}
