<?php
use App\Friend;
use App\Block;
use App\Profile;
use App\Summary;
use App\User;
use App\Category;
use App\Proof;
use App\Backer;

$loggedUser = Auth::user()->id;
$isOwner = false;
$isFriend = false;
$who = "";
$friend_button = '<button disabled class="navButton">Request Connection</button>';

//Retrieve the Profile object attributed to this profile owner
$profile = Profile::where('user_id','=',$profileOwner->id)->first();
//Retrieve the Executive Summary of this profile owner
$summary = Summary::where('user_id','=',$profileOwner->id)->first();
//Retrieve the Proof of Concept for this profile owner
$proof = Proof::where('user_id','=',$profileOwner->id)->first();
//Retrieve the List of Backers for this profile owner
$backers = Backer::where('backing_id','=',$profileOwner->id)->get();
//Pull up the list of the profile owner's friends
$friends = Friend::where('user2','=',$profileOwner->id)->where('accepted','=','1')
->orWhere('user1','=',$profileOwner->id)->where('accepted','=','1')->get();
//Verify if this profile belongs to the one visiting the page
if($profileOwner->id == Auth::user()->id)
{
  $isOwner = true;
  $who = "Your";
}
else
{
  $who = $profileOwner->name."'s";
  //Check to see if the profile owner and logged in user are friends
  $friend_check = Friend::where('user1','=',$loggedUser)->where('user2','=',$profileOwner->id)->where('accepted','=','1')
  ->orWhere('user1','=',$profileOwner->id)->where('user2','=',$loggedUser)->where('accepted','=','1')->get();

  //Friend  and Block button logic for profile
  if($friend_check != "[]")//If the friend check is not empty
  {
    $isFriend = true;
    $friend_button = '<button class="navButton" id="unfriend">Disconnect</button>';
  }
  else
  {
    $isFriend = false;
    $friend_button = '<button class="navButton" id="friend">Request Connection</button>';
  }
}
$parent = "WHERE parent IS NULL";
$categories = DB::select(DB::raw('SELECT * FROM categories '.$parent.' ORDER BY name ASC'));
?>
@extends('layouts.master')

@section('title')
  {{$profileOwner->name}} | DCN
@endsection

@section('content')
  @include ('layouts.errors')
  <br/>
  <div class="row">
    <!--PROFILE INFO/OPTIONS-->
    <div class=" col-sm-2 profileLeft">
      <div id="profile_pic_box">
        @if($isOwner==true)
          <!--FORM TO CHANGE AVATAR-->
          <a id="editAvatar" href="#" onclick="return false;" onmousedown="toggleElement('avatar_form')">Edit Avatar</a>
          <form id="avatar_form" enctype="multipart/form-data" method="post" action="/photoSystem/<?php echo Auth::user()->name?>">
            {{csrf_field()}}
            <h4>Change your avatar</h4>
            <input type="file" name="avatar" required>
            <p><input type="submit" value="Upload"></p>
          </form>
        @endif
        <?php
          if($profileOwner->avatar == NULL)
            echo '<img src="/images/Default.jpg" width="245px" height="245px" alt="Profile Picture"><br/>';
          else
            echo '<img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$profileOwner->avatar.'" width="250px" height="250px" alt="Profile Picture"><br/>';
        ?>
      </div>
      <hr/>
      <div id="titles"><!--The display of basic user information-->
        <center><strong><p>{{$profileOwner->name}}</p></strong></center>
        <p><strong>Role:</strong> {{$profileOwner->role}}</p>
        <p><strong>Name:</strong> {{$profileOwner->first}} {{$profileOwner->last}}</p>
        @if($summary != "")
        <p><strong>Business Name:</strong><br/>{{$summary->product_name}}</p>
        <p><strong>Target Market:</strong><br/> {{$summary->market}} {{$summary->subMarket}}</p>
        @endif
      </div>
      <hr/>
      <div id="quickNav">
        <button type="button" class="navButton" data-toggle="modal" data-target="#connectionsModal">View Connections</button>
        @if($isOwner == true)
          <button type="button" class="navButton" data-toggle="modal" data-target="#summaryModal">Edit Summary</button>
          <button type="button" class="navButton" data-toggle="modal" data-target="#settingsModal">Account Settings</button>
        @else
          <?php echo $friend_button;//Possible loaction for mentor and mentee buttons?>
        @endif
      </div>
    </div>
    <div class="col-sm-10 profileRight" data-offset="20">
      <div id="projectContent">
        <br>
        <div id="tabs">
          <button id ="tab1" class="tablinks">Executive Summary</button>
          <button id ="tab2" class="tablinks">Proof of Concept</button>
          <button id ="tab3" class="tablinks">List of Backers</button>
        </div>
        <center><!--The following divs contain the user business model information-->
          <div id="exSummary">
            <br/>
            @if($summary == "")
              <h5>{{$profileOwner->name}} has yet to post their executive summary.</h5>
            @else
              <h2>Executive Summary</h2>
              <hr>
              <h5>-Market Information-</h5>
              <div class="summaryShow"><p><strong>Business Name:</strong> {{$summary->product_name}}</p></div>
              <div class="summaryShow"><p><strong>Target Market:</strong> {{$summary->market}}</p></div>
              <div class="summaryShow"><p><strong>Target Age Range:</strong> {{$summary->age_range}}</p></div>
              <div class="summaryShow"><p><strong>Target Region:</strong> {{$summary->region}}</p></div>
              <div class="summaryShow"><p><strong>Other:</strong> {{$summary->market_other}}</p></div>
              <hr>
              <h5>-Market Activity Information-</h5>
              <div class="summaryShow"><p><strong>Top Competitor:</strong> {{$summary->competitor1}}</p></div>
              <div class="summaryShow"><p><strong>Other Competitor:</strong> {{$summary->competitor2}}</p></div>
              <div class="summaryShow"><p><strong>Other Competitor:</strong> {{$summary->competitor3}}</p></div>
              <hr>
              <h5>-Risks & Exit Strategy Information-</h5>
              <div class="summaryShow"><p><strong>Risk Analysis:</strong> {{$summary->risks}}</p></div>
              <div class="summaryShow"><p><strong>Exit Strategy:</strong> {{$summary->exit_strategy}}</p></div>
              <div class="summaryShow"><p><strong>Return on Investment:</strong> {{$summary->ROI}}</p></div>
              <div class="summaryShow"><p><strong>Business Model Liquidity:</strong> {{$summary->liquidity}}</p></div>
            @endif
          </div>
          <div id="proofConcept">
            <br/>
            <h3>Proof of Concept
              @if($isOwner == true)
              <a href="#" data-toggle="popover" data-trigger="hover"
              data-content="Use this section to display a Proof of Concept for your business model.
              Be it an elevator pitch or a full on presentation, utilize this embedding section to paste
              in a video representation of your business model.">(?)</a>
              @endif
            </h3>
            <hr>
            @if($proof == "")
              <h5>{{$profileOwner->name}} has yet to add a form of Proof of Concept...</h5>
            @else
              <div class="proof"><?php echo $proof->embed;?></div>
            @endif
            @if($isOwner==true)
              <div>
                <form id="proofForm" enctype="multipart/form-data" method="post" action="/proofSystem">
                  {{csrf_field()}}
                  <input type="hidden" name="user" value="{{Auth::user()->name}}">
                  <div class="form-group">
                    <input type="text" id="newProof" name="newProof" size="60" placeholder="Click here to add an embed link">
                  </div>
                  <div class="form-group">
                    <button type="submit" id="contentButton"  class="btn btn-default">
                      Update
                    </button>
                  </div>
                </form>
              </div>
            @endif
          </div>
          <div id="backers">
            <br/>
            <h3>List of Backers</h3><hr>
            @if($isOwner==true)
              <form onsubmit="return false;">
                <div class="form-group">
                  <input type="text" id="enterBacker" size="60" placeholder="Click here to add a backer to your list"><br/>
                </div>
                <div class="form-group">
                  <button type="submit" id="addBacker" class="btn btn-default">Add Backer</button>
                </div>
              </form>
              <span id="backStatus"></span>
              <hr/>
            @endif
            @if($backers == "[]")
              <h5>{{$profileOwner->name}} has yet to provide a List of Backers...</h5>
            @else
              <div>
                <ul id="backersList">
                  @foreach($backers as $backer)
                    <li id="{{$backer->id}}" class="backer">
                      {{$backer->backer_name}} since (Time period)&nbsp;
                      @if($isOwner == true)
                        <button class="btn btn-default" onclick="remove('{{$backer->id}}','{{$backer->backer_name}}')">Delete</button>
                      @endif
                    </li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
        </center>
      </div>
    </div>
  </div>

  <!--LIST OF CONNECTIONS MODAL-->
  <div id="connectionsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{$who}} Connections</h4>
        </div>
        <div class="modal-body">
          <?php
            foreach($friends as $friend)
            {
              $buddy = "";
              if($friend->user1 == $profileOwner->id)
                $buddy = $friend->user2;
              else if($friend->user2 == $profileOwner->id)
                $buddy = $friend->user1;

              $guy =  User::where('id','=',$buddy)->first();
              $user1avatar = $guy ->avatar;
              $user1pic = '<img src="/uploads/user/'.$guy->id.'/images'.'/'.$user1avatar.'" alt="'.$guy->name.'" class="user_pic">';
          		if($user1avatar == NULL)
              {
                $picURL = "/images/Default.jpg";
          			$user1pic = '<img src="'.$picURL.'" alt="'.$guy->name.'" class="user_pic">';
          		}
              //echo '<div><a href="/profile/'.$guy->name.'">'.$user1pic.'</a><b><p>'.$guy->name.'</p></b></div>';
              echo '<div class="friendrequests">
                      <a href="/profile/'.$guy->id.'">'.$user1pic.'</a>
                      <div class="user_info"><b><p>'.$guy->name.'</p></b>
                      </div>
                    </div><hr/>';
            }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--EXECUTIVE SUMMARY MANAGEMENT MODAL-->
  <div id="summaryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create your Executive Summary</h4>
        </div>
        <div class="modal-body">
          <form id="executiveSummaryForm" enctype="multipart/form-data" method="post" action="/summarySystem"><!--onsubmit="return false;"-->
            {{csrf_field()}}
            <input type="hidden" name="user" value="{{Auth::user()->name}}">
            <center>
              @include ('layouts.errors')
            </center>
            <h6>
              Market Information
              <a href="#" data-toggle="popover" data-trigger="hover"
              data-content="When breaking into a market, it is important to refrain from a general scope.
              Knowing which socioeconomic, gender(s), occupation(s),region(s) and/or lifestyle(s) to go for
              vastly improves your success.  You will often find that people tend to dislike being lumped together
              in general markets so it is best to be as specific as possible.">(?)</a>
            </h6><hr>
            <div class="form-group">
              <label for="category"><strong>Select an Industry/Market that describes your business/service</strong></label><br/>
                <?php
                  echo "<select id='categories' name=\"market\" onmouseup='showSub()'>";
                  echo "<option value='dummy' disabled selected>Select your option</option>";
                  foreach($categories as $category)
                  {
                    echo "<option value='".$category->name."'>".$category->name."</option>";
                  }
                  echo "</select><br/>";
                  foreach($categories as $category)
                  {
                    echo "<div style='display:none;' id='".$category->name."'><select name='submarket'>";
                    echo "<option value='dummy2' disabled selected>Select an advanced category (Optional)</option>";
                    $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
                    foreach($subCategory as $sub)
                    {
                      echo "<option value='".$sub->name."'>".$sub->name."</option>";
                    }
                    echo "</select></div>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="title"><strong>Please provide a title for your business/service.</strong></label><br/>
              <input type="text" id="summaryTitle" name="title">
            </div>
            <div class="form-group">
              <label for="age"><strong>Provide a target age range if applicable.</strong></label><br/>
              <input type="text" id="age" name="age">
            </div>
            <div class="form-group">
              <label for="region"><strong>Provide a target region is applicable.</strong></label><br/>
              <input type="text" id="region" name="region">
            </div>
            <div class="form-group">
              <label for="markother"><strong>Provide additional information about your target market here.</strong>
                <a href="#" data-toggle="popover" data-trigger="hover"
                data-content="Use this space include more information about your target market that is pertinent,
                vital and specific to your business/service.">(?)</a>
              </label><br/>
              <textarea type="text" id="markother" name="markother" rows="5" cols="60"></textarea>
            </div>
            <h6>
              Market Activity Information
              <a href="#" data-toggle="popover" data-trigger="hover"
              data-content="It is important to know your competition in order reduce risk, time, resources used and expenses.
              Help potential investors understand who they are/may be (Direct/Alternative/Available Spend),
              their Strengths and Weaknesses (Market share vs yours/Buyer perception of their product/Spending power/Speed of
              Innovation) and what they plan to do next (Annual forecasts/Promo and Ads/Market Trends/Product rollouts).">(?)</a>
            </h6><hr>
            <div class="form-group">
              <label for="compete1"><strong>Competitor 1</strong></label><br/>
              <textarea id="compete1" name="compete1" rows="5" cols="60"></textarea>
            </div>
            <div class="form-group">
              <label for="compete2"><strong>Competitor 2</strong></label><br/>
              <textarea type="text" id="compete2" name="compete2" rows="5" cols="60"></textarea>
            </div>
            <div class="form-group">
              <label for="compete3"><strong>Competitor 3</strong></label><br/>
              <textarea type="text" id="compete3" name="compete3" rows="5" cols="60"></textarea>
            </div>
            <h6>Risks & Exit Strategy Information</h6><hr>
            <div class="form-group">
              <label for="risk"><strong>Enter your risk assessment here.</strong>
                <a href="#" data-toggle="popover" data-trigger="hover"
                data-content="Explain what could go wrong and what must go right. Outline vulnerablities
                and which assets need protection. Where do the great legal exposures lie?
                What is the frequency of each risk occuring and their impacts?  How should they be managed?">(?)</a>
              </label><br/>
              <textarea type="text" id="risk" name="risk" rows="5" cols="60"></textarea>
            </div>
            <div class="form-group">
              <label for="exit"><strong>Enter your exit strategy here.</strong>
                <a href="#" data-toggle="popover" data-trigger="hover"
                data-content="It is always best to know where to stop, or at least have plans
                for the unforseeable occurences of life. Consider the following questions. How much is the company worth
                and how much will stakeholders recieve? Do you believe you should ever sell, if so, when?
                Should you go public or get acquired? Employee stock options?
                Disband and walk away or pass on to your heirs?">(?)</a>
              </label><br/>
              <textarea type="text" id="exit" name="exit" rows="5" cols="60"></textarea>
            </div>
            <div class="form-group">
              <label for="roi"><strong>Provide your Return on Investment report here.</strong>
                <a href="#" data-toggle="popover" data-trigger="hover"
                data-content="What should investors expect to get back for what you seek from them? Include
                annual and quarerly returns as well.">(?)</a>
              </label><br/>
              <textarea type="text" id="roi" name="roi" rows="5" cols="60"></textarea>
            </div>
            <div class="form-group">
              <label for="liquid"><strong>Discuss Liquidity options here.</strong>
                <a href="#" data-toggle="popover" data-trigger="hover"
                data-content="Discuss the current, quick and cash ratios in regards to resolving liability issues
                if applicable.">(?)</a>
              </label><br/>
              <textarea type="text" id="liquid" name="liquid" rows="5" cols="60"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" id="contentButton" onclick="addContent()" class="btn btn-default">
                Update Executive Summary
              </button>
              <span id="status"></span>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('javascript')
  <script type="text/javascript">
    var token = '{{Session::token()}}';
    var urlf = '{{route('friend')}}';
    var urlb = '{{route('backer')}}';

    function toggle(type)
    { //Functionality for the friend button
      var $tog = $('#'+type);
      var user = "<?php echo $profileOwner->id?>";
      var log = "<?php echo Auth::user()->id?>";

      $tog.html("please wait...");

      if(type == "friend" || type == "unfriend")
      {
        $.ajax(
        {
          method: 'POST',
          url: urlf,
          data: {type: type, user: user, log: log, _token: token}
        }).done(function (msg)
        {
          //console.log(msg['message']);
          if(msg['message'] == "friend_request_sent")
            $tog.html('Connection Request Sent');
          else if(msg['message'] == "unfriend_ok")
            $tog.html('Disconnected');//$tog.html('<button id="friend">Request As Friend</button>');
          else
          {
            alert(msg['message']);
            $tog.html('Try again later.')
          }
        });
      }

      if(type == "block" || type == "unblock")//This block is no longer necessary since blocking won't be a function
      {//It remains strictly for the purpose of reference for the future
        $.ajax(
        {
          method: 'POST',
          url: urlb,
          data: {type: type, user: user, log: log, _token: token}
        }).done(function (msg)
        {
          //console.log(msg['message']);
          if(msg['message'] == "blocked_ok")
          {
            $tog.html('Blocked');//$tog.html('<button id="unblock">Unblock User</button>');
          }
          else if(msg['message'] == "unblocked_ok")
          {
            $tog.html('Unblocked');//$tog.html('<button id="block">Block User</button>');
          }
          else
          {
            alert(msg['message']);
            $tog.html('Try again later.')
          }
        });
      }
    }
    function remove(backer, name)//This function is for the instant removal of backers from the user's list(AJAX)
    {
      $('#backStatus').html("");
      var back = $('#'+backer);
      $.ajax(
      {
        method: 'POST',
        url: urlb,
        data: {backer: backer, delete: 'yes', _token: token}
      }).done(function (msg)
      {
        //console.log(msg['message']);
        if(msg['message'] == "del_success")
        {
          back.hide();
          $('#backStatus').html(name+" has been removed from your Backer's List");
        }
        else
          $('#backStatus').html(msg['message']);
      });
    }
    $(document).ready(function()
    {
      var $friend = $('#friend');
      var $block = $('#block');
      var $unfriend = $('#unfriend');
      var $unblock = $('#unblock');
      var $edit = $('#editAvatar');

      $edit.hide();
      $('#exSummary, #proofConcept, #backers').hide();
      $('#exSummary').show();
      $friend.on('click', function(){toggle('friend');});
      $unfriend.on('click',function(){toggle('unfriend');});
      $('[data-toggle="popover"]').popover();//This allows the text boxes to appear upon hovering the "?"
      $('#profile_pic_box').mouseover(function(){$edit.show();});
      $('#profile_pic_box').mouseout(function(){$edit.hide();});

      $('#tab1, #tab2, #tab3').on('click', function()
      {//Tab functionality for the business information content
        $('#exSummary, #proofConcept, #backers').hide();
        switch (this.id)
        {
          case 'tab1':
            $('#exSummary').show();
          break;
          case 'tab2':
            $('#proofConcept').show();
          break;
          case 'tab3':
            $('#backers').show();
          break;
        }
      });
      $('#addBacker').on('click', function()//Inatsnt addition to the backers list (AJAX)
      {
        $('#backStatus').html("");
        var list = $('#backersList');
        var backing = "<?php echo Auth::user()->id;?>";
        var backer = $('#enterBacker');
        if(backer.val() != "")
        {
          $.ajax(
          {
            method: 'POST',
            url: urlb,
            data: {backing: backing, backer: backer.val(), _token: token}
          }).done(function (msg)
          {
            //console.log(msg['message']);
            if(msg['message'] == "back_success")
              list.append("<li class='backer'>"+backer.val()+"since (Time Period)</li>");
            else
              $('#backStatus').html(msg['message'])
          });
        }
      });
    });
    function showSub()//Categories still needs work, this is old code that needs to be worked on
    {
      var categories = document.getElementById('categories');
      var list = categories.value;
      if(list != "dummy")
      {
        var sub = document.getElementById(list);
        //console.log("List is: "+list);

        if(sub.style.display == "none")
          sub.style.display = "block";
        else if(sub.style.display == "block")
          sub.style.display = "none";
      }
    }
  </script>
@endsection

<!--
Market breaking into[string]
  -Male/Female/Both
  -Age range
  -Geography(Does it matter?)
  -Annual income range
  -Profession(Does it matter?)
  -Other aspects
  **Cannot be general anymore, must taget a socioeconomic, gender, occupation , region or lifestyle
  **Can also target by generation, cohort or certain lifestages
  **People hate being lumped together in general market now so it's best
    to be as specific as possible when breaking out to fulfill a need-->
<!--Activity level of this market (percieved)[text]
  **Determine competition
  **It is important to know in order reduce risk, time, resources used and expense
  -Who are the competitors?(Determine the consumer's alternatives)
    >Direct Competitors = in the eyes of the consumer, your product and thiers is interchangeable
    >Substitute/Alternative Competitors = same service in a different category(different region/more remote)
    >Competitors that don't do the same but still pick at your business
    >"Available Spend" competitors = "same occassion", popcorn vs soda at the movies
  -What are their strengths and weaknesses?(Show that it's worth a toe to toe)
    >Know their market share compared to yours
    >How targeted buyers percieve thier products/services
    >Their financial strength(spending power on ads, promos etc)
    >Ability and speed of innovation
  -What are the competitor's planning to do next?
    >Annual forcast of sales
    >Promotion and advertising programs
    >Introduction, support, and success of new products and services
    >Market, product, or service category, and sub-category trends
    >Direction for future growth-->
<!--
Risks and how they can be avoided[text]
  -What could go wrong?
  -What must go right?
  -Where are we vulnerable?
  -Which assets need protection?
  -What is our greatest legal exposure?
  **High Risk Transactions
  -Risk Analysis
    >Frequency of risk occuring
    >Impact of risk
    >How should risks be managed
    >Annual Returns
Exit Strategy
  -How much is the company worth?
  -What will each stakeholder receive?
  -Should you sell?
    >When do you sell?
  -Go public or get acquired?
  -Sell to Employees?
  -Pass controll to your heirs?
  -Disband and walk away?
  -What is your Business Liquidity?
RIO - Profit - Loss of this venture[string?]
Liquidity
  -Current Ratio = ability to satisfy its short-term liabilities with its short-term assets
  -Quick Ratio = add together your business’s available cash, its accounts receivable, and its
                 short-term or marketable securities and divide that number by its current liabilities
  -Cash Ratio = available cash and short-term securities when determining current assets
-->
