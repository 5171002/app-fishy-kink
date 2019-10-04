<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

require "/vagrant/source/func/FKMongo.php";

class FabChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $db = connect_mongo();
        $tweet = $request->input("tweetID");
        $user = $request->input("userID");
        $fablist = $db["tweetDB"]->findOne(["_id" => $tweet["tweetID"]])["fabUser"];
        if (in_array($user["userID"],$fablist)){    //もし、すでにファボしていればリストから削除する
            //削除
            $fablist = array_diff($fablist,$user["userID"]);
            //indexを詰める
            $fablist = array_values($fablist);
        } else {
            //追加
            array_push($fablist,$user["userID"]);
        };
        //更新
        $db["tweetDB"]->updateOne(["_id" => $tweet["tweetID"]],['$set'=>["fabUser" => $fablist]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $db = connect_mongo();
        $tweetID = $request->input("tweetID");
        $user["userID"] = $request->input("user["userID"]");
        $fablist = $db["tweetDB"]->findOne(["_id" => $tweet["tweetID"]])["fabUser"];
        if (in_array($user["userID"],$fablist)){    //もし、すでにファボしていればリストから削除する
            //削除
            $fablist = array_diff($fablist,$user["userID"]);
            //indexを詰める
            $fablist = array_values($fablist);
        } else {
            //追加
            array_push($fablist,$user["userID"]);
        };
        //更新
        $db["tweetDB"]->updateOne(["_id" => $tweet["tweetID"]],['$set'=>["fabUser" => $fablist]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
