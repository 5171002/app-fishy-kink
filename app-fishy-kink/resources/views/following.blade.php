<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>followers</title>
<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/Follow.css">

</head>
<body>
    <!-- <div class="search-main">
        <div class="search">
            <form action="">
                <input type="text" value="" >
                <input type=submit value="検索">
            </form>
        </div> -->

        <div class="tabs">
        <!-- <input id="follower" type="radio" name="tab_item" checked>
        <label class="tab_item" for="follower">フォロワー</label> -->

        <input id="follow" type="radio" name="tab_item" checked>
        <label class="tab_item" for="follow">フォロー中</label>

    <div class="tab_content" id="follow_content">
        @isset($followingData)
            @isset($followingData["follow"][0])
                @if(count($userProfile["follow"]) == 1)
                            <ul class ="list_none">
                                <li>
                                <a onclick="location.href='/profile?user={{$followingID}}'"><img src='{{$followingImg}}'/></a>
                                        {{$followingName}}    
                                    <button class="word_btn" type="button" onclick="location.href='/profile?user={{$followingID}}'">
                                        <span>@</span>{{$followingID}}
                                    </button>
                                    <div class="profilePro">{{$followingPro}}</div>
                                </li>
                            </ul>
                @elseif(count($userProfile["follow"]) > 1)     
                    @foreach ($followingData["follow"] as $key => $following)
                        <ul class ="list_none">
                            <li>
                            <a onclick="location.href='/profile?user={{ $following }}'"><img src='{{ $followingImg[$key]}}'/></a>
                                    {{$followingName[$key]}}    
                                <button class="word_btn" type="button" onclick="location.href='/profile?user={{ $following }}'">
                                <span>@</span>{{ $following }}
                                </button>
                                    
                                <div class="profilePro">
                                    {{
                                        $followingPro[$key]
                                        ,$key = $key + 1   
                                    }} 
                                </div>
                            </li>
                        </ul>
                    @endforeach
                @endif
            @endisset
        @endisset
                    
    </div>
    <div>
        <button  class="btn-square" type="button" onclick="location.href='/profile'">戻る</button>
    </div>  

    <!-- <div class="tab_content" id="followerS_content">
           
    </div> -->

</body>
</html>
