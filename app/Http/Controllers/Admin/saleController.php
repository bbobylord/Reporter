<?php

namespace App\Http\Controllers\Admin;

use App\Commodity;
use App\Http\Controllers\Controller;
use App\Http\Requests\saleRequest;
use App\Sale;
use App\Supplier;
use Illuminate\Http\Request;
use Morilog\Jalali\CalendarUtils;

class saleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $counter=1;
        $sales=Sale::latest()->paginate(15);
        return view('Admin.sale.index',compact('sales','counter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $commoditis  = Commodity::all();
        return view('Admin.sale.create',compact('commoditis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(saleRequest $request)
    {


        //---------------------------------Date------------------------
        ///convert number Persin to English
        $date_en = $this->convert_numbers($request->date);
        ///explode date and save to vaiable
        $persian_date = explode("/",   $date_en);
        $year=$persian_date[0];
        $month = $persian_date[1];
        $day = $persian_date[2];

        ///convert Persian date to  miladi
        $miladiDate = CalendarUtils::toGregorian($year,$month, $day);
        ///impload by / sepreator  [2020/2/2]
        $DateConvert = implode('/', $miladiDate);
        //-------------------------------end Date------------------------

        ////-----------------Kye gnerate -------------
        $codeSaler=$this->generateKye();
        $codeBuyer=$this->generateKye();

        ////-----------------end Kye gnerate -------------


        $sale=Sale::create([

            'buyerName'=>$request->buyerName,
            'buyerCity'=>$request->buyerCity,
            'sellerCode'=> $codeSaler,
            'buyerCode'=> $codeBuyer,
            'phoneBuyer'=>$request->phoneBuyer,
            'price'=>$request->price,
            'created_at'=>$DateConvert
        ]);
        $sale->commodities()->attach(request('commodity_id'));

        alert()->success('با موفقیت افزوده شد');
        return redirect('sale');

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        return view('Admin.sale.show',compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        $commoditis  = Commodity::all();
        return view('Admin.sale.edit',compact('sale','commoditis'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(saleRequest $request, Sale $sale)
    {

        ///check date Database == $request->date
        if ($sale->created_at != $request->date ){
            ///convert number Persin to English
            $date_en = $this->convert_numbers($request->date);
            ///explode date and save to vaiable
            $persian_date = explode("/",   $date_en);
            $year=$persian_date[0];
            $month = $persian_date[1];
            $day = $persian_date[2];

            ///convert Persian date to  miladi
            $miladiDate = CalendarUtils::toGregorian($year,$month, $day);
            ///impload by / sepreator  [2020/2/2]
            $DateConvert = implode('/', $miladiDate);

        }else{
            $DateConvert=$request->date;
        }





        $sale->update([

            'buyerName'=>$request->buyerName,
            'buyerCity'=>$request->buyerCity,
            'phoneBuyer'=>$request->phoneBuyer,
            'price'=>$request->price,
            'created_at'=>$DateConvert
        ]);
        $sale->commodities()->sync(request('commodity_id'));

        alert()->success('با موفقیت بروز رسانی  شد');
        return redirect('sale');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        alert()->error('باموفقیت حذف شد!!!');
        return back();
    }


    public function search (Request $request)
    {
        $counter=1;
        $keyWord = request('search');
        $sales = Sale::search($keyWord)->latest()->paginate(15);
        return view('Admin.sale.index',compact('sales','counter'));
    }


    /*
  * generate SalerCode automatic
  */
    public function generateKye()
    {
        $code_id= rand(10,100000);
        $salerCode= Sale::where('sellerCode',"$code_id")
            ->where('buyerCode',"$code_id")->first();

        if ( $salerCode > 1)
        {
            return rand(10,100000);
        }else{
            return  $code_id;
        }
    }




    /*
     *  function for convert persionNumber to english number
     */
    public  function convert_numbers($input)
    {
        $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹',',');
        $english = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',',');
        $string = str_replace($persian, $english, $input);
        return $string;
    }
}
