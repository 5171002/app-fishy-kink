<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>home</title>
<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="">
<link rel="shortcut icon" href="">
<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="font/css/open-iconic-bootstrap.css">

<style>
  .accordion .inner {display: none;}
  .accordion p {cursor: pointer;}
  .accordion {display:inline;}
</style>

<script>
var count = 0;
var result;
var tweetCount;

/******************************************************************* 1秒ごとにツイートの数を取得し数に変動があった場合にアラート表示 *******************************************************************/
$(function(){ // 遅延処理
  setInterval((function update(){ //1000ミリ秒ごとに実行
    $.ajax({
      type: 'POST',
      url: '/api/reloadTweet',    // url: は読み込むURLを表す
      dataType: 'json',           // 読み込むデータの種類を記入
      data: {userID:'',
            _token: '{{ csrf_token() }}'
            },
      cache: false
      }).done(function (results) {
        // 通信成功時の処理
        
        result = results;

        if(count == 0){
          dispTweets(result);
          count++;        
          tweetCount = results.length;
        }

        console.log(result);
        console.log(tweetCount);
        if(tweetCount != results.length){
          // アラートの追加
          document.getElementById('alertContents').innerHTML = '<div class="alert alert-info" role="alert">' + 
                                                                  '<a href="#" class="alert-link">新しいツイート</a>' +
                                                                '</div>';
        }
      }).fail(function (err) {
        // 通信失敗時の処理
        alert('ファイルの取得に失敗しました。');
      });
      return update;
      
  }()),1000);
});

/******************************************************************* ファボ *******************************************************************/
$(function(){
  $("#centerContents").on('click',".fab",function() {
    var tweetid = $("#centerContents > #tweetID").val();
    console.log(tweetid);
    $.ajax({
      type: 'POST',
      url: '/api/fabChange',
      dataType: 'json',
      data: {
        userID: "test" , 
        tweetID:tweetid , 
        _token:'{{ csrf_token() }}'
      },
      cache: false
    }).done(function(results){
      // alert('成功しました。');
      console.log(results);
    });
  });
});

/******************************************************************* リツイート *******************************************************************/
  $("#centerContents").on('click',".normalReTweet",function() {
    // var tweetid = $("#centerContents > #tweetID").val();
    var tweetid = $(this).parents(".accordion").prevAll("#tweetID").val();
    console.log(tweetid);
    var push_button = this;
    $.ajax({
      type: 'POST',
      url: '/api/reTweetChange',
      dataType: 'json',
      data: {
        userID: "{{ session('userID') }}", 
        tweetID: tweetid, 
        _token:'{{ csrf_token() }}'
      },
      cache: false
    }).done(function(results){
      //アコーディオンを閉じる処理
      $(push_button).parents(".inner").slideToggle();
      if (results["message"] == "add"){
        $(push_button).parents().prevAll(".reTweet").children().css("color","green");
        $(push_button).text("リツイートを取り消す");
      }else{
        $(push_button).parents().prevAll(".reTweet").children().css("color","gray");
        $(push_button).text("リツイート");
      }
  });
});

/******************************************************************* ツイート表示 *******************************************************************/
function dispTweets(results){
   $('#centerContents').empty();

  let tweetType = "";

  results.forEach(function(tweet){

    $('#centerContents').append('<input id="tweetID "type="hidden" value='+ tweet["_id"]["$oid"]+ ' />')
    $('#centerContents').append('<div class="tweet card">');  
    
    // リツイート 
    if (tweet["type"] == "retweet") {
      tweetType = '<div class="retweet-user">'+ tweet["userID"] + 'さんがリツイートしました</div>';
    } 
            
    else {
      tweetType = ""
    }
      $('#centerContents').append(
          '<div class="tweetTop card-header">'+
              '<div class="tweet-user">' +
              '</div>' +
              tweetType + 
              '<a href=/profile?user=' + tweet["userID"] +'>'+
                  tweet["userID"] +
              '</a> '+
            '<div class="time">'
                  + tweet["time"] + 
              '</div> '+
          '</div>');
    $('#centerContents').append('<div class="tweetMain card-body">'+ tweet["text"] + '</div>');

    // 画像表示
    $('#centerContents').append('<div style=float:left>');
    if (tweet["type"] == "tweet"){
      countImg = tweet["img"].length;
    }else{
      countImg = 0;
    }
    for(var i=0;i<countImg;i++){
      $('#centerContents').append('<img src="' + tweet["img"][i] + '"width="200" height="150" />');
    }
    $('#centerContents').append('</div><p>');

    $('#centerContents').append('<div class="tweetBottom d-inline">');

    $('#centerContents').append('<button class=reply type=button><span class="oi oi-action-undo" style="color:blue;"></span> </button></div>');
    
    var iconColor = "";
    var reTweetText = "";

    if (tweet["type"] == "tweet"){
      if (tweet["retweetUser"].indexOf("{{ session('userID') }}") == -1){
        iconColor = "gray";
        reTweetText = "リツイート";
      }else{
        iconColor = "green";
        reTweetText = "リツイートを取り消す";
      }
    }else{
      //とりあえず
        iconColor = "pink";
        reTweetText = "これはリツイートです";
    }

    $('#centerContents').append('<div class="accordion">' +
                                    '<button class=reTweet type=button><span class="oi oi-loop" style="color:'+iconColor+';"></span> </button>' +

                                    '<div class="inner">' +
                                      '<a class=normalReTweet type=button>'+reTweetText+'</a>' +
                                      '<a href=javascript:open2()>🖊コメントつけてリツイート</a>' +
                                    '</div>' +
                                '</div>'); 
    

    var tweet_json = JSON.stringify(tweet["_id"])

    if (tweet["type"] == "tweet"){
      if (tweet["fabUser"].indexOf("{{ session('userID') }}") == -1){
        iconColor = "gray";
      }else{
        iconColor = "red";
      }
    }else{
      iconColor = "pink";
    }
    $('#centerContents').append('<button class=fab type=button><span class="oi oi-heart" style="color:'+iconColor+';"></span> </button></div>');
  });
}

/******************************************************************* 新しいツイートの表示 *******************************************************************/
$(function(){ // 遅延処理
<<<<<<< HEAD
  $(document).on("click", ".alert-link", function () {
    $.ajax({
      type: 'POST',
      url: '/api/reloadTweet',
      dataType: 'json',           
=======
  $('#qqqq').click(function () {
  // setInterval((function update(){ //1000ミリ秒ごとにupdateという関数を実行する
    $.ajax({
      type: 'POST',
      url: '/api/reloadTweets',    // url: は読み込むURLを表す
      dataType: 'json',           // 読み込むデータの種類を記入
>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2
      data: {userID:'',
            _token: '{{ csrf_token() }}'
            },
      cache: false
      }).done(function (results) {
<<<<<<< HEAD

        dispTweets(result);
        
=======
        // 通信成功時の処理
        
        $('#centerContents').empty();

        let tweetType = "";

        console.log(results);

        results.forEach(function(tweet){

          $('#centerContents').append('<input id="tweetID" type="hidden" value='+ tweet["_id"]["$oid"]+ ' />')
          $('#centerContents').append('<div class="tweet card">');  
          
          // リツイート 
          if (tweet["type"] == "retweet") {
            
            tweetType = '<div class="retweet-user">'+ tweet["userID"] + 'さんがリツイートしました</div>';
            // $.ajax({
            //   type: 'POST',
            //   url: '/api/getTweet',
            //   dataType : 'json',
            //   data: {
            //     tweetID : tweet["originTweetID"],
            //     _token  : '{{ csrf_token() }}'
            //   },
            //   cache:false
            // }).done(function(originTweet){
            //   tweet = originTweet["tweet"];
            //   console.log(tweet);
            // });
          }else{
            tweetType = ""
          }
          console.log(tweet);
            $('#centerContents').append(
                '<div class="tweetTop card-header">'+
                    '<div class="tweet-user">' +
                    '</div>' +
                    tweetType + 
                    '<a href=/profile?user=' + tweet["userID"] +'>'+
                        tweet["userID"] +
                    '</a> '+
                   '<div class="time">'
                        + tweet["time"] + 
                    '</div> '+
                '</div>');
          $('#centerContents').append('<div class="tweetMain card-body">'+ tweet["text"] + '</div>');
          // 画像表示
          $('#centerContents').append('<div style=float:left>');
          if (tweet["type"] == "tweet"){
            countImg = tweet["img"].length;
          }else{
            countImg = 0;
          }
          for(var i=0;i<countImg;i++){
            $('#centerContents').append('<img src="' + tweet["img"][i] + '"width="200" height="150" />');
          }
          $('#centerContents').append('</div><p>');
          $('#centerContents').append('<div class="tweetBottom d-inline">');
          
          $('#centerContents').append('<button class=reply type=button><span class="oi oi-action-undo" style="color:blue;"></span> </button></div>');
          
          var iconColor = "";
          var reTweetText = "";

          if (tweet["type"] == "tweet"){
            if (tweet["retweetUser"].indexOf("{{ session('userID') }}") == -1){
              iconColor = "gray";
              reTweetText = "リツイート";
            }else{
              iconColor = "green";
              reTweetText = "リツイートを取り消す";
            }
          }else{
            //とりあえず
              iconColor = "pink";
              reTweetText = "これはリツイートです";
          }

          $('#centerContents').append('<div class="accordion">' +
                                          '<button class=reTweet type=button><span class="oi oi-loop" style="color:'+iconColor+';"></span> </button>' +

                                          '<div class="inner">' +
                                            '<a class=normalReTweet type=button>'+reTweetText+'</a>' +
                                            '<a href=javascript:open2()>🖊コメントつけてリツイート</a>' +
                                          '</div>' +
                                      '</div>'); 

          if (tweet["type"] == "tweet"){
            if (tweet["fabUser"].indexOf("{{ session('userID') }}") == -1){
              iconColor = "gray";
            }else{
              iconColor = "red";
            }
          }else{
            iconColor = "pink";
          }
          $('#centerContents').append('<button class=fab type=button><span class="oi oi-heart" style="color:'+iconColor+';"></span> </button></div>');
          
         
      });
      // $('#main-contents').text(results);
>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2
      }).fail(function (err) {
        // 通信失敗時の処理
        alert('ファイルの取得に失敗しました。');
      });
    });
<<<<<<< HEAD
    $("#alert-link").remove();
=======

>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2
});

/******************************************************************* アコーディオンの閉じたり開いたり *******************************************************************/
$(document).on("click", ".reTweet", function () {
  
  //クリックされた.accordion2の中のp要素に隣接する.accordion2の中の.innerを開いたり閉じたりする。

  $(this).next('.accordion2 .inner').slideToggle();
  //クリックされた.accordion2の中のp要素以外の.accordion2の中のp要素に隣接する.accordion2の中の.innerを閉じる
  $('.accordion2').not($(this)).next('.accordion2 .inner').slideUp();
});

/******************************************************************* 別タブで表示 *******************************************************************/
function open1() {
    var w = ( screen.width-600 ) / 2;
    var h = ( screen.height-600 ) / 2;
    window.open("/tweet", "hoge", "width=600, height=500" + ",left=" + w + ",top=" + h, "location=no");
  }

/******************************************************************* 別タブで表示２（仮） *******************************************************************/
function open2() {
    window.open("/tweet", "hoge", "width=600, height=600 , location=no");
  }

</script>
</head>
<body>
    <div id="menu row d-inline col-md-12"> 
        <button type="button" class="link_button btn page-link text-dark d-inline-block" onclick="location.href='/home'">home</button>
        <button type="button" class="link_button btn page-link text-dark d-inline-block"  onclick="location.href='/notify'">通知</button>
        <button type="button" class="link_button btn page-link text-dark d-inline-block"  onclick="location.href='/DM'">メッセージ</button>
        <button type="button" class="link_button btn page-link text-dark d-inline-block"  onclick="location.href='/story'">ストーリー</button>
        <input type="image" class="link_button btn page-link text-dark d-inline-block" onclick="location.href='/profile'"
        src="{{ $userIcon }}" height="40" width="40" class="img-thumbnail"
        style="width: auto; padding:0; margin:0; background:none; border:0; font-size:0; line-height:0; overflow:visible; cursor:pointer;"
        >
        </button>
        <button type="button" class="btn btn-default"> <font color="red"> <span class="oi oi-magnifying-glass"></span> 検索 </font></button>

        <form method='get' action="/search" class="form-inline d-inline" >
            <!-- <div class="form-group"> -->
                <input class="form-control" type=text name="searchString">
                <input class="form-control" type=submit value="検索">
            <!-- </div> -->
        </form>
        <button type="button" class="link_button btn page-link text-dark d-inline-block" target=”_blank” onclick='open1();'">ツイート</button>
<<<<<<< HEAD
=======
        
        
        
>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2
        <button type="button" class="link_button btn page-link text-dark d-inline-block" onclick="location.href='/logout'">ログアウト</button>
    </div>
    <div id="alertContents"></div>
    <div class="row">
        <div id="leftContents" class="col-sm-3"></div>
<<<<<<< HEAD
        <div id="centerContents" class="col-sm-6"></div>
=======
        <div id="centerContents" class="col-sm-6">
            <div class="tweet card">                
            @foreach ($tweets as $tweet)
                <div class="tweetTop card-header">
                @if ($tweet["type"] == "retweet")
                    <div class="retweet-user">{{ $tweet["userID"] }}さんがリツイートしました</div>
                @endif
                <a name=user href="/profile?user={{ $tweet['userID'] }}" >{{ $tweet['userID'] }}</a>
                <div class="time"> {{ $tweet["time"] }}</div>
                        <!-- <div class="date">{{ explode(" ",$tweet["time"])[0] }}</div> 　
                        <div class="time">{{ explode(" ",$tweet["time"])[1] }}</div> -->
                </div>
                <div class="tweetMain card-body">

                  @isset($tweet["text"])
                    {{ $tweet["text"] }}
                  @endisset               

                </div>
                  
                <div style = float: left>
                @isset($tweet["img"][0])
                    @foreach($tweet["img"] as $img)
                     <img src=" {{ $img }}" width="200" height="150"/>
                    @endforeach
                @endisset
                </div>
                <div class="tweetBottom d-inline">
                    <div class="reply d-inline-block">
                      <input name="reply" type="image" src="images/reply.jpg" onclick="reply()" alt="リプライ">
                    </div>
                    <div class="retweet d-inline-block">
                      <input name="retweet" type="image" src="images/retweet.png" onclick="retweet()" alt="リツイート"/>
                    </div>
                    <div class="fab d-inline-block">
                      <input  class="fab" name="fab" type="image" src="images/faboDis.jpg"  alt="いいね"/>
                    </div>
                </div>
            @endforeach
            

            </div>
        </div>
>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2
        <div id="rightContents" class="col-sm-3"></div>
</body>
<img class="" height="100" width="100" 
<<<<<<< HEAD
        src="images/twitter.jpg" />
=======
        src="images/twitter.jpg"
        />
</html>
<script type="text/javascript">
  function open1() {
    window.open("/tweet", "hoge", "width=600, height=600 , location=no");
  }
</script>
<script type="text/javascript">
  function open2(count) {
    window.open("/tweet", "hoge", "width=600, height=600 , location=no");
  }

</script>


<script>
$(function(){ // 遅延処理
  setInterval((function update(){ //1000ミリ秒ごとにupdateという関数を実行する
    $.ajax({
      type: 'POST',
      url: '/api/reloadTweets',    // url: は読み込むURLを表す
      dataType: 'json',           // 読み込むデータの種類を記入
      data: {userID:'',
            _token: '{{ csrf_token() }}'
            },
      cache: false
      }).done(function (results) {
        // 通信成功時の処理

        let tweetCount = 162;
        console.log(results.length);
        if(tweetCount != results.length){
          // $('#alertContents').append('<div class="alert alert-info" role="alert">' + 
          //                               '<a href="#" class="alert-link">新しいツイート</a>' +
          //                             '</div>');
          document.getElementById('alertContents').innerHTML = '<div class="alert alert-info" role="alert">' + 
                                                                '<a href="#" class="alert-link">新しいツイート</a>' +
                                                                '</div>';
        }


      }).fail(function (err) {
        // 通信失敗時の処理
        alert('ファイルの取得に失敗しました。');
      });
      return update;
  }()),100000);
});
</script>

<script>
$(document).on("click", ".reTweet", function () {
  
  //クリックされた.accordion2の中のp要素に隣接する.accordion2の中の.innerを開いたり閉じたりする。
  $(this).next('.accordion .inner').slideToggle();
>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2

</html>

<<<<<<< HEAD
=======
});
</script>

>>>>>>> 81185f713811487a5b05bf117cd177407ba713e2
