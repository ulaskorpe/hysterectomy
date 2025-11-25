<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Jackiedo\Cart\Facades\Cart;

class CheckCartHasItemsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next): Response
    {

        $items = Cart::getItems();

        if( count($items) === 0 ){

            return redirect("/")->with('info',__('Sepetinizde ürün bulunmamaktadır.'));

        }
        return $next($request);
    }
}
