<?php

namespace App\Http\Controllers;

use App\Models\CommandeDetails;
use App\Http\Requests\StoreCommandeDetailsRequest;
use App\Http\Requests\UpdateCommandeDetailsRequest;
use App\Http\Resources\CommandeDetailsResource;

class CommandeDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = CommandeDetails::all();
        return CommandeDetailsResource::collection($details);
    }

    public function getDetailsByCommandId($id)
    {
        $details = CommandeDetails::with(['product' => function ($query) {
            $query->select('id', 'title', 'images');
        }])->where('commande_id', $id)->get();
        return CommandeDetailsResource::collection($details);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommandeDetailsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommandeDetailsRequest $request)
    {
        $detail = new CommandeDetails();
        $detail->title =$request->input('title');
        $detail->description =$request->input('description');
        $detail->quantity =$request->input('quantity');
        $detail->product_id =$request->input('product_id');
        $detail->commande_id =$request->input('commande_id');
        $detail->price =$request->input('price');
        $detail->priceUni =$request->input('priceUni');
        $detail->details =$request->input('details');
        $detail->save();

        return new CommandeDetailsResource($detail);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CommandeDetails  $commandeDetails
     * @return \Illuminate\Http\Response
     */
    public function show(CommandeDetails $commandeDetails)
    {
        return new CommandeDetailsResource($commandeDetails);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CommandeDetails  $commandeDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(CommandeDetails $commandeDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommandeDetailsRequest  $request
     * @param  \App\Models\CommandeDetails  $commandeDetails
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommandeDetailsRequest $request, CommandeDetails $commandeDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CommandeDetails  $commandeDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(CommandeDetails $commandeDetails)
    {
        //
    }
}
