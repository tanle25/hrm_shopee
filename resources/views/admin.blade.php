@extends('welcome')

@section('content')
<section class="py-16 bg-gray-100 dark:bg-gray-800">
    <div class="max-w-4xl px-4 mx-auto ">
        <div class="p-6 bg-white rounded-md shadow dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-6 text-xl font-medium leading-6 text-gray-900 dark:text-gray-300">Personal Information
            </h2>
            <form action="{{url('get-product')}}" method="post" class="">
                @csrf
                <div class="container px-4 mx-auto"></div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium dark:text-gray-400" for="">Cookie Shopee</label>
                    <textarea
                        class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded dark:text-gray-400 dark:border-gray-900 dark:bg-gray-800"
                        name="cookie" rows="5" placeholder="Write something..."></textarea>
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium dark:text-gray-400" for="">
                        URL
                    </label>
                    <input
                        class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded dark:text-gray-400 dark:placeholder-gray-500 dark:border-gray-800 dark:bg-gray-800"
                        type="text" name="url" placeholder="URL">
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
    </div>
</section>
@endsection
