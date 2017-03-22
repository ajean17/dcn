@extends('layouts.master')

@section('title')
  Star Gazer | DCN
@endsection

@section('content')
  <h1>Search Page</h1>
@endsection

@section('javascript')
<script>
//ajax rq
/*
given category c1, find the most similar category
Best score variable set to minimum value o
for each other category c2
  set matchscore to 0
  for each Feature
    compare c1 and c2 on Feature
    if match, matchscore++
  if matchscore > bestscore
  then bestmatch = c2 and bestscore = matchscore
return bestmatch
*/
</script>
@endsection
