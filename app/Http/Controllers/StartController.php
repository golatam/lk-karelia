<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StartController extends CommonController
{
    protected $entity = 'start';

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            $this
                ->setCollect([
                    'titleIndex' => __("{$this->entity}.start_title"),
                ]);

            return $next($request);
        });
    }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard');
    }
}
