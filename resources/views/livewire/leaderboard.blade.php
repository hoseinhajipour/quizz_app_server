<div>
    <ul>
    @foreach($Leaderboards as $Leaderboard)
       <li>{{$Leaderboard->username}} - {{$Leaderboard->score}}</li>
    @endforeach
    </ul>
</div>

