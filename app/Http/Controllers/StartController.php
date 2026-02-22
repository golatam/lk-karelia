<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * @return \Illuminate\Contracts\View\Factory
     */
    public function __invoke(Request $request)
    {
        $this
            ->setCollect([])
        ;

        return view("start", $this->getCollect());
    }
}
