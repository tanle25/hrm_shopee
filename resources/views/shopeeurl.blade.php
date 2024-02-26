@extends('welcome')

@section('content')
<section class="py-16 bg-gray-100 dark:bg-gray-800">
    <div class="max-w-4xl px-4 mx-auto ">
        <div class="p-6 bg-white rounded-md shadow dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-6 text-xl font-medium leading-6 text-gray-900 dark:text-gray-300">Shopee Scraper
            </h2>
            <form action="{{url('get-product-shopee')}}" method="post" class="">
                @csrf
                <div class="container px-4 mx-auto"></div>
                {{-- <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium dark:text-gray-400" for="">Source</label>
                    <textarea
                        class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded dark:text-gray-400 dark:border-gray-900 dark:bg-gray-800"
                        name="content" rows="5" placeholder="Write something..."></textarea>
                </div> --}}

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium dark:text-gray-400" for="">
                        URL
                    </label>
                    @error('url')
                    <div
                        class="relative block w-full p-4 mb-4 text-base leading-5 text-white bg-red-500 rounded-lg opacity-100 font-regular">
                        {{ $message }}
                    </div>

                    @enderror
                    <input
                        class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded dark:text-gray-400 dark:placeholder-gray-500 dark:border-gray-800 dark:bg-gray-800"
                        type="text" name="url" placeholder="Shopee">
                        <input
                        class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded dark:text-gray-400 dark:placeholder-gray-500 dark:border-gray-800 dark:bg-gray-800"
                        type="text" name="lazada" placeholder="Lazada">
                        <input
                        class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded dark:text-gray-400 dark:placeholder-gray-500 dark:border-gray-800 dark:bg-gray-800"
                        type="text" name="tiki" placeholder="Tiki">
                </div>




                <div class="mt-7">
                    <div class="flex justify-start space-x-2">
                        <button type="submit"
                            class="inline-block px-6 py-2.5 bg-blue-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-600">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- </div> --}}
    <div class=" max-w-4xl px-4 pt-10 mx-auto flex justify-end">
        <a class=" p-3 bg-blue-600 hover:bg-blue-400 rounded-lg text-white" href="{{url('export')}}">Export Excel</a>
    </div>
    <div class=" container mx-auto my-9">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 m-5">
            @if (\Session::has('products'))
            @foreach (\Session::get('products') as $product )
            <div class="md:flex border dark:border-gray-300 dark:bg-gray-900 my-3">
                <div class="md:flex-shrink-0">
                    <img class=" h-32 w-full object-cover"
                        src="https://down-vn.img.susercontent.com/file/{{$product->image}}" alt="Event image">
                </div>
                <div class=" p-4 flex-1">
                    <p class="block mt-1 text-lg dark:text-white leading-tight font-medium text-black">
                        {{html_entity_decode($product->title)}}</p>
                    <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">

                        @if ($product->price_min < 0)
                        {{number_format($product->price)}}
                        @else
                        {{number_format($product->price_min)}}
                        - {{ number_format($product->price_max)}}
                        @endif

                    </div>

                </div>
                <div class=" w-10 flex items-center">
                    <a class=" dark:text-white text-gray-900" href="{{url('xoa-san-pham/'.$product->id)}}">Xoá</a>
                </div>
            </div>
            @endforeach

            @endif
        </div>

    </div>


</section>
@endsection
