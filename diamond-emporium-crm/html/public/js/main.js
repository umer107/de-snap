// JavaScript Document

$(document).ready(function () {
// Leads multi select calendar
debugger
var pageNumber = getParameterByName("page");

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
if(pageNumber == 'RoomManager')
  {
    //$('.calendarLink').trigger('click')
    debugger
    suggestedDate();
    $('nav.mainMenu li').removeClass('current');
    $('.calendarLink').closest('li').addClass('current');
    $('.dashboard-header, .leadsContainer, .leadDeailContainer, .newLead, .leavesContainer, .newLeaveContainer').addClass('hide');

    var getHtml = $('.NewCalendarContainer').html();
    var setHtml = "<div class='NewCalendarContainer full relative'>";
    setHtml += getHtml;
    setHtml += "</div>";
    setHtml += "<div class='hide fixed addBookingPopup'></div>";
    $('.calendarLoad').html(setHtml);
    $('.calendarLoad .bookingHeading').addClass('hide');
    $('.calendarLoad').removeClass('hide');
    $('.all-rooms').trigger('click');
    setTimeout(function(){ 
      alert('working')
      $('.calendarLoad input.newLeadCalendar').addClass('newLeadCalendarBooking').removeClass('newLeadCalendar');
     }, 1000);
    
  }
var userisOnDashBoard = $('.contentArea').hasClass('hide');
if(userisOnDashBoard && pageNumber != 'RoomManager')
{
  $('.mainMenu li').removeClass('current');
  $('.dashboardLink').closest('li').addClass('current');
}
/*------------------------------------------------------------------*/
/*---------------------Start Onload Function------------------------ */
/*------------------------------------------------------------------*/
    

  $("#email").attr('onfocusout','onFocusOuts()');
  $("#phonenumber").attr('onfocusout','onFocusOutsPhone()');
  var countries = [
     { value: 'Andorra', data: 'AD' },
     // ...
     { value: 'Zimbabwe', data: 'ZZ' }
  ];


  $('#aaautocomplete').autocomplete({
      lookup: countries,
      onSelect: function (suggestion) {
          alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
      }
  });


    function GetCountriesList() {

        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetCountriesList",
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }
                
                
                var setHtml = '';
                var setHtml2 = '';
                for (var i = 0; i < parsed.length; i++) {
                  var countryName = parsed[i].country_name;
                  setHtml += "<li><a href='javascript:;' value='"+countryName+"'>"+countryName+"</a></li>";
                  setHtml2 += "<option value='"+countryName+"'>"+countryName+"</option>";

                }
                //$('.countryList').html(setHtml);
                $('#combobox, #partnerCombobox').html(setHtml2);
                window.comboboxList = setHtml2;
                
                setTimeout(function(){ 
                  $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input, .partnerCountryDiv .ui-state-default, .partnerCountryDiv .ui-autocomplete-input').val('Australia');
                  window.getBasicInfo = $('.basicInfo').html();
                 }, 3000);

                

            }

        });    
        
    }
    GetCountriesList();

    function GetTeamStatus() {
        //$budget = '$2-5K';
        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetTeamStatus",
            success: function (data) {
                // convert json into Array

                var parsed = '';               
                  try{
                    parsed = JSON.parse(data);                  
                  }           
                  catch(e)                  
                  {                   
                  return false;                  
                  }
                var arr = [];
                
                for(var x in parsed){
                  arr.push(parsed[x]);
                }
                var getcount =  arr.length;

                var teamStatus = "";
                var teamAbsence = 0;
                for (var i = 0; i < arr.length; i++) 
                {

                  // User Image
                  var getUserimage = arr[i][0].image;
                  if(getUserimage == "" || getUserimage == null)
                  {
                      getUserimage = 'sampleUser.png';
                  }

                  // Team Absences
                  

                  // Status Image
                  var statusImage = arr[i][0].user_status;
                  var LeaveStatus = arr[i][0].LeaveStatus;

                  var setStatusImage = ""
                  if(LeaveStatus == "yes")
                  {
                    setStatusImage = 'statusImage'
                  }
                  else
                  {
                    if(statusImage == "Supplier")
                    { 
                      setStatusImage = 'supplier'
                    }
                    else if(statusImage == "Workshop")
                    { 
                      setStatusImage = 'workshop'
                    }
                    else if(statusImage == "Other")
                    { 
                      setStatusImage = 'other'
                    }
                    else if(statusImage == "Available")
                    { 
                      setStatusImage = 'available'
                    }
                    else if(statusImage == "Lunch")
                    { 
                      setStatusImage = 'lunch' 
                    }
                    else if(statusImage == "Lunch15")
                    { 
                      setStatusImage = 'lunchAt15' 
                    }
                    else if(statusImage == "Lunch30")
                    { 
                      setStatusImage = 'lunchAt30' 
                    }
                    else if(statusImage == "Lunch45")
                    { 
                      setStatusImage = 'lunchAt45' 
                    }
                    else if(statusImage == "Lunch60")
                    { 
                      setStatusImage = 'lunchAt60' 
                    }
                  }
                  teamStatus += '<div class="full" value="menuAvailable">';
                  teamStatus += '<span><img src="/profile_image/'+getUserimage+'"></span>';
                  //******************GETTEAMSTATUS******************************//
                  if(arr[i][0].status == 0 || arr[i][0].status == null)
                  {
                      teamStatus += '<div><p class="fs-13 robotomedium ellipsis" title="'+arr[i][0].UserFullName+'">'+arr[i][0].UserFullName+'</p><p class="fs-14 color-orange ellipsis" title="'+arr[i][0].user_status+'">'+arr[i][0].user_status+'</p></div>';
                      
                  }
                  else
                  {
                      teamStatus += '<div><p class="fs-13 robotomedium ellipsis" title="'+arr[i][0].UserFullName+'">'+arr[i][0].UserFullName+'</p><p class="fs-14 color-orange ellipsis" title="'+arr[i][0].status+'">'+arr[i][0].status+'</p></div>';
                  }
                  //******************GETTEAMSTATUS******************************//
                  if(LeaveStatus == "yes")
                  {
                    teamAbsence++;
                    var getLeaveDate = arr[i][0].Leave_StartDate
                    var getCompleteLeaveDate = new Date(getLeaveDate);  
                    var getOnlyDate = getCompleteLeaveDate.getDate();
                    teamStatus += '<label class="annualLeave d-i-b"><span class="fs-11 robotobold">'+getOnlyDate+'</span><span class="fs-8 robotolight">Day</span></label>';  
                  }
                  else
                  {
                    teamStatus += '<label class="lunchImage '+setStatusImage+'"></label>';
                  }
                  
                  if(teamAbsence == 1)
                  { 
                    $('.noOfAbsence').html(1 + ' absence'); 
                  }
                  else if(teamAbsence > 1)
                  { 
                    $('.noOfAbsence').html(teamAbsence + ' absences');
                  }
                  else
                  { 
                    $('.noOfAbsence').html(0 + ' absence');
                  }

                  teamStatus += '</div>';
                  
                }
                $('.absebceDropdown').html(teamStatus);
            }

        });    
        
    }  
    GetTeamStatus()


    setInterval(function()
    { 
      //$('.absebceDropdown').slideUp(50);
      GetTeamStatus(); 
    }, 300000);




    function GetNextInLine(userBudget) {
       
        //$budget = '$2-5K';
        
        $budget = userBudget;
        $.ajax({
            type: "GET",
            url: "/dashboard/GetNextInLine",
            data: {budget: $budget},
            success: function (data) {
                // convert json into Array
                
                var parsed = '';          
                try{                           
                  parsed = JSON.parse(data);              
                }                 
                catch(e)                
                {                  
                  return false;                  
                }

                $('.assignToDiv a.selected-text').attr('value','All');
                $('.assignToDiv a.selected-text span').html('*Assign to');
                var arr = [];
                
                for(var x in parsed){
                  arr.push(parsed[x]);
                }


                arr.sort(function(a, b) {
                    var textA = a["0"].items.user_name;
                    var textB = b["0"].items.user_name;
                    return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                });

                // Get Smaller lead count number
                
                var makeList = [];
                for(i=0; i < arr.length; i++)
                {
                    makeList.push(arr[i][0].count);
                }
                
                var getSmallestNumber =  Math.min.apply(null, makeList);
                var repititionCheck = 1000000000000;
                var repititionCheckAdded = 1000000000000;

                // Append Agent list into agent dropdown

                var dropdownList = "";
                // local
                
                for(i=0 ; i < arr.length; i++)
                {

                    
                    if(arr[i][0].count == getSmallestNumber)
                    {
                        var getUserimage = arr[i][0].items.image;
                        var getUserName = arr[i][0].items.user_name;

                        // Get short code for Agent Name Start

                        var res = getUserName.split(" ");
                        var firstNameWord = res[0];
                        var middleNameWord = res[1];
                        var lastsNameWord = res[2];
                        var firstLetterFirstName = firstNameWord.charAt(0);
                        var middleLetterFirstName = '';
                        var lastLetterFirstName = '';

                        if(middleNameWord != null){ middleLetterFirstName = middleNameWord.charAt(0); }
                        if(lastsNameWord != null){ lastLetterFirstName = lastsNameWord.charAt(0); }
                        
                        if(lastsNameWord != null)
                        {
                          var shortCode = firstLetterFirstName+middleLetterFirstName+lastLetterFirstName;
                        }
                        else
                        {
                          var shortCode = firstLetterFirstName+middleLetterFirstName;
                        }
                        
                        // Get short code for Agent Name End

                        if(getUserimage == "" || getUserimage == null)
                        {
                            getUserimage = 'sampleUser.png';
                        }
                        dropdownList += '<li><a href="javascript:;" id="'+arr[i][0].items.user_id+'" value="'+getUserName+'" shortcode="'+shortCode+'"><span><img class="pull-left" src="/profile_image/'+getUserimage+'"><span><div><label>Next in line:</label><label>'+getUserName+'</label></div></span></span></a></li>';
                        $('.otherSelection .inlineAgentImg').attr('src', '/profile_image/'+getUserimage);
                        $('.otherSelection .inlineAgentName').html(getUserName);
                        var getAgentOptions = $('.agentOptions').html();
                        dropdownList += getAgentOptions;
                        break;
                    }
                }

                var Adding = true;
                for(i=0 ; i < arr.length; i++)
                {
                    var getUserimage = arr[i][0].items.image;
                    var getUserName = arr[i][0].items.user_name;

                    // Get short code for Agent Name Start

                    var res = getUserName.split(" ");
                    var firstNameWord = res[0];
                    var middleNameWord = res[1];
                    var lastsNameWord = res[2];
                    var firstLetterFirstName = firstNameWord.charAt(0);
                    var middleLetterFirstName = '';
                    var lastLetterFirstName = '';

                    if(middleNameWord != null){ middleLetterFirstName = middleNameWord.charAt(0); }
                    if(lastsNameWord != null){ lastLetterFirstName = lastsNameWord.charAt(0); }

                    if(lastsNameWord != null)
                    {
                    var shortCode = firstLetterFirstName+middleLetterFirstName+lastLetterFirstName;
                    }
                    else
                    {
                    var shortCode = firstLetterFirstName+middleLetterFirstName;
                    }

                    // Get short code for Agent Name End

                    if(getUserimage == "" || getUserimage == null)
                    {
                        getUserimage = 'sampleUser.png';
                    }
                    
                    if(arr[i][0].count == getSmallestNumber)
                    {   
                        if(repititionCheck == repititionCheckAdded)
                        {
                            Adding = false;
                            repititionCheckAdded++
                        }
                    else
                        {
                            Adding = true;
                        }
                    }
                    else
                    {
                        Adding = true;
                    }
                    
                    if(Adding == true)
                    {

                        dropdownList += '<li><a href="javascript:;" id="'+arr[i][0].items.user_id+'" value="'+getUserName+'" shortcode="'+shortCode+'"><span><img class="pull-left" src="/profile_image/'+getUserimage+'"><span><div><label>Next in line:</label><label>'+getUserName+'</label></div></span></span></a></li>';
                    }
                }

                $('.assignToDiv ul.dropdownOptions').html(dropdownList);
                $('ul.assignToDiv.dropdown li:first-child').addClass('nextInline');

            }

        });    
        
    }

    function GetUserBasedOnBudget(userBudget) {
        //$budget = '$2-5K';
        $budget = userBudget;
        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetUserBasedOnBudget",
            data: {budget: $budget},
            success: function (data) {
                // convert json into Array
                
                var parsed = '';          
                try{                           
                  parsed = JSON.parse(data);              
                }                 
                catch(e)                
                {                  
                  return false;                  
                }
                $('.assignToDiv a.selected-text').attr('value','All');
                $('.assignToDiv a.selected-text span').html('*Assign to');
                var arr = [];
                
                for(var x in parsed){
                  arr.push(parsed[x]);
                }

                // Get Smaller lead count number
                var makeList = [];
                for(i=0; i < arr.length; i++)
                {
                    makeList.push(arr[i].count);
                }
                
                var getSmallestNumber =  Math.min.apply(null, makeList);
                var repititionCheck = 1000000000000;
                var repititionCheckAdded = 1000000000000;

                // Append Agent list into agent dropdown

                var dropdownList = "";
                // local
                
                for(i=0 ; i < arr.length; i++)
                {

                    
                    if(arr[i].count == getSmallestNumber)
                    {
                        var getUserimage = arr[i].items.image;
                        var getUserName = arr[i].items.user_name;

                        if(getUserimage == "" || getUserimage == null)
                        {
                            getUserimage = 'sampleUser.png';
                        }
                        dropdownList += '<li><a href="javascript:;" id="'+arr[i].items.user_id+'" value="'+getUserName+'"><span><img class="pull-left" src="/profile_image/'+getUserimage+'"><span><div><label>Next in line:</label><label>'+getUserName+'</label></div></span></span></a></li>';
                        $('.otherSelection .inlineAgentImg').attr('src', '/profile_image/'+getUserimage);
                        $('.otherSelection .inlineAgentName').html(getUserName);
                        var getAgentOptions = $('.agentOptions').html();
                        dropdownList += getAgentOptions;
                        break;
                    }
                }

                var Adding = true;
                for(i=0 ; i < arr.length; i++)
                {
                    var getUserimage = arr[i].items.image;
                    var getUserName = arr[i].items.user_name;
                    if(getUserimage == "" || getUserimage == null)
                    {
                        getUserimage = 'sampleUser.png';
                    }
                    
                    if(arr[i].count == getSmallestNumber)
                    {   
                        if(repititionCheck == repititionCheckAdded)
                        {
                            Adding = false;
                            repititionCheckAdded++
                        }
                    else
                        {
                            Adding = true;
                        }
                    }
                    else
                    {
                        Adding = true;
                    }
                    
                    if(Adding == true)
                    {

                        dropdownList += '<li><a href="javascript:;" id="'+arr[i].items.user_id+'" value="'+getUserName+'"><span><img class="pull-left" src="/profile_image/'+getUserimage+'"><span><div><label>Next in line:</label><label>'+getUserName+'</label></div></span></span></a></li>';
                    }
                }

                $('.assignToDiv ul.dropdownOptions').html(dropdownList);
                $('ul.assignToDiv.dropdown li:first-child').addClass('nextInline');
                //window.GetAdditionalDetails = $('.additional-details').html();
                //window.getNewLeadAll = $('.newLead').html();
            }

        });    
        
    }


/*------------------------------------------------------------------*/
/*--------------------- End Onload Function------------------------ */
/*------------------------------------------------------------------*/

/*------------------------------------------------------------------*/
/*--------------------- Start Create New Lead --------------------- */
/*------------------------------------------------------------------*/

    // user dp menu dropdown
    //window.GetAdditionalDetails = $('.additional-details').html();
    $(document).on('click', '.user-dp-Dropdown', function () { 
      //$('.basicInfo span').slideDown(300);
       if($('.userDropdown').hasClass('loadingContent'))
       {}
        else
        {
            $('.userDropdown').slideToggle(300);
          
        }
    });// End

    // user menu dp dropdown select

    $(document).on('click', '.userDropdown div p img', function () { 
      
        $('.userDropdown div p img').removeClass('active');
        $(this).addClass('active');
        var getValue = $(this).attr('value');
    });// End

    $(document).on('click', '.userDropdown div', function () {  
        
        var getValue = $(this).attr('value');       
        if(getValue == "menuLunch")
        {      

            var getLunchTime = $('.userDropdown div p img').filter('.active').attr('value');
            var getTimeValue = $('.userDropdown div p img').filter('.active').attr('data-value');
            $('.menuLunch .displayPicture img').attr('src',getLunchTime);
            $(this).attr('data-value',getTimeValue);
        }
        
      
      var userStatusValue = getValue.replace('menu' , '');
      if(userStatusValue == 'Lunch' && getTimeValue == undefined)
      {
          return false;
          userStatusValue = 'Lunch';
      }
      else if(userStatusValue == 'Lunch' && getTimeValue == 15)
      {
          userStatusValue = 'Lunch15';
      }
       else if(userStatusValue == 'Lunch' && getTimeValue == 30)
      {
          userStatusValue = 'Lunch30';
      }
       else if(userStatusValue == 'Lunch' && getTimeValue == 45)
      {
          userStatusValue = 'Lunch45';
      }
       else if(userStatusValue == 'Lunch' && getTimeValue == 60)
      {
          userStatusValue = 'Lunch60';
      }
      $('.user-list').addClass('hide');
      $('.' + getValue).removeClass('hide');
      /*-----------------------------------------------------*/
      //User Update Ajax Call
       $.ajax({
        type: "POST",
        url: "/ajaxuserstatusupdate",
        data: {status: userStatusValue},
        success: function (data) {}
        });    
       $('.userDropdown').slideUp(300);
       return false;
    /*-----------------------------------------------------*/  
      
    });// End

/*-----------------------------------------------------*/


    // close userdropdown on outside click 
    $(document).on('click', function(event){
        var container = $(".user-dp-Dropdown");
        if (!container.is(event.target) &&            
            container.has(event.target).length === 0)
            {
                if($('.userDropdown').is(':visible'))
                {$('.userDropdown').slideUp(150);}
            }
    });// End

/*-----------------------------------------------------*/

    /* Absense dropdown Start*/
    
    $(document).on('click', '.userAbsence', function () { 
      
      $('.absebceDropdown').slideToggle(300);  
      
    });// End


    // close userdropdown on outside click 
    $(document).on('click', function(event){
        var container = $(".userAbsence");
        if (!container.is(event.target) &&            
            container.has(event.target).length === 0)
            {
                if($('.absebceDropdown').is(':visible'))
                {$('.absebceDropdown').slideUp(150);}
            }
    });// End

    /* Absense dropdown End*/

/* ----------------------------------------------------*/

    //AJAX USER STATUS CHECK
   function getUserStatus()
   {    
      
    $.ajax({
    type: "GET",
    url: "/ajaxGetUserStatus",
    data: '{}', 
    success: function (data) {
        
        var parseResult = '';              
        try{    
          parseResult = JSON.parse(data);               
        }
        catch(e)
        {               
          return false;                  
        }

        if(parseResult.role_id == "3" || parseResult.role_id == "4" || parseResult.role_id == "6" || parseResult.role_id == "7" || parseResult.role_id == "8"  || parseResult.role_id == "9")
        {
          $('.dashboard-header a.new-Leave').addClass('opacity0').hide();
        }

        $('.userDropdown').addClass('loadingContent'); 
        var checkUserImage = parseResult.image;
        if(checkUserImage == null)
        {
          $('#displayUserPicture img').attr("src", "/profile_image/sampleUser.png" );
        }
        else
        {
          $('#displayUserPicture img').attr("src", "/profile_image/"+ checkUserImage );
        }
        
        //******SetProfileUserName********/
        var firstName = parseResult.first_name;
        var lastName = parseResult.last_name;
        if(firstName != null && lastName != null )
        {
            $("#ProfileName").text(firstName+' '+lastName);
        }
        else
        {
        $("#ProfileName").text();    
        }       
        //******SetProfileUserName********/
        
        if(parseResult.user_status === "Available")
        {
            //alert("Available");
            $('.userDropdown div[value="menuAvailable"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
        }
        else if(parseResult.user_status === "Lunch")
        {
            
            $('.userDropdown div[value="menuLunch"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
        }
         else if(parseResult.user_status === "Lunch15")
        {   
            //alert("Lunch15");
            
            $('.userDropdown div[value="menuLunch"] img[data-value="15"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
            $('.menuLunch .displayPicture img').attr('src','/images/lunch15.svg');
            
            setInterval(function(){
              //alert("Available");
                $.ajax({
                type: "POST",
                url: "/ajaxuserstatusupdate",
                data: {
                    
                status: 'Available'
                
                },
               success: function(data) {
                   
                  $('.userDropdown div[value="menuAvailable"]').trigger('click');
                  $('.userDropdown').removeClass('loadingContent');
                  $('.user-dp-Dropdown').removeClass('hide');
                     
                 }
                });
            }, 900000);

        }
         else if(parseResult.user_status === "Lunch30")
        {
            //alert("Lunch30");
            $('.userDropdown div[value="menuLunch"] img[data-value="30"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
            $('.menuLunch .displayPicture img').attr('src','/images/lunch30.svg');
            
             setInterval(function(){
              //alert("Available");
                $.ajax({
                  type: "POST",
                  url: "/ajaxuserstatusupdate",
                  data: {
                      
                  status: 'Available'
                  
                  },
                 success: function(data) {
                     
                $('.userDropdown div[value="menuAvailable"]').trigger('click');
                $('.userDropdown').removeClass('loadingContent');
                $('.user-dp-Dropdown').removeClass('hide');
                     
                 }
                });
              }, 1800000);
          
        }
         else if(parseResult.user_status === "Lunch45")
        {
            //alert("Lunch45");
            $('.userDropdown div[value="menuLunch"] img[data-value="45"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
            $('.menuLunch .displayPicture img').attr('src','/images/lunch45.svg');
           
            setInterval(function(){
              //alert("Available");
                $.ajax({
                    type: "POST",
                    url: "/ajaxuserstatusupdate",
                    data: {
                        
                    status: 'Available'
                    
                    },
                   success: function(data) {
                       
                  $('.userDropdown div[value="menuAvailable"]').trigger('click');
                  $('.userDropdown').removeClass('loadingContent');
                  $('.user-dp-Dropdown').removeClass('hide');
                       
                   }
                  });
                }, 2700000);
           
        }
         else if(parseResult.user_status === "Lunch60")
        {
            //alert("Lunch60");
            $('.userDropdown div[value="menuLunch"] img[data-value="30"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
            $('.menuLunch .displayPicture img').attr('src','/images/lunch60.svg');
          
           setInterval(function(){
              //alert("Available");
                $.ajax({
                  type: "POST",
                  url: "/ajaxuserstatusupdate",
                  data: {
                      
                  status: 'Available'
                  
                  },
                 success: function(data) {
                     
                $('.userDropdown div[value="menuAvailable"]').trigger('click');
                $('.userDropdown').removeClass('loadingContent');
                $('.user-dp-Dropdown').removeClass('hide');
                     
                 }
                });
              }, 3600000);

        }
        else if(parseResult.user_status === "Workshop")
        {
            $('.userDropdown div[value="menuWorkshop"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
        }
        else if(parseResult.user_status === "Supplier")
        {
            $('.userDropdown div[value="menuSupplier"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
        }
         else if(parseResult.user_status === "Other")
        {
            $('.userDropdown div[value="menuOther"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
        }

        }        
    });   
   }

   //This will be run on page Load

   getUserStatus();

   //Set Interval of 1 minute on function of getUserStatus()

   setInterval(function(){
        getUserStatus();  // this will run after every 1 minute
   }, 900000);

/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    $(document).on('focus', '.formfields input', function () { 
      //$('.basicInfo span').slideDown(300);
      //$('.add-address, .next-saveDiv').slideDown(300);  
      
    });// End

    // Showing top headings
    $(document).on('keyup', '.formfields input, .customerFields input', function () {
      if($(this).val().length > 0)
      {
        $(this).prev('span').slideDown(150);
      }    
    });// End


/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    // Validating firstName and Last Name
    $(document).on('keyup', '.basicInfo input.firstname, .basicInfo input.lastname', function () {
      $(this).closest('.relative').find('.requiredError').addClass('opacity0');
        var getName = $(this).val();
        if ($.trim(getName).length == 0) {
            $(this).next('label.firstError').addClass('opacity0');
            $(this).removeClass('hasError');
        }
        else if (isValidNames(getName)) {
            $(this).next('label.firstError').addClass('opacity0');
            $(this).removeClass('hasError');
            //validateBasicInfo();
        }
        else {
            $(this).next('label.firstError').removeClass('opacity0');
            $(this).addClass('hasError');
        }
    });// End


/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    //Validating Phone Number
    $(document).on('keyup', '.basicInfo input.phonenumber', function () {
         $('.phonefield .firstError,.phonefield .requiredError,.phonefield .phoneexists').addClass('opacity0');
        var getphone = $(this).val();
        var getphoneLength = $(this).val().length;
        if(getphone == 0)
        {$(this).next('label').addClass('opacity0');}
        else if(!validatePhone(getphone)) { 
            $(this).next('label.firstError').removeClass('opacity0');
        }
        else
            {
                $(this).next('label.firstError').addClass('opacity0');
            }
    });// End



/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

  // Validating Email and opening next screen buttons
  $(document).on('keyup', '.basicInfo input.checkEmailCounts', function () {
      $('#email').next().addClass('opacity0').next('.requiredError').addClass('opacity0');
      $('.emailexists, .emailDiv .requiredError').addClass('opacity0');
      var getValue = $(this).val().length;
      var getemail = $(this).val();
      if ($.trim(getemail).length == 0) {
          $(this).next('label').next('label').addClass('opacity0');
      }
      else if (isValidEmailAddress(getemail)) {
          $(this).next('label').next('label').addClass('opacity0');
          //validateBasicInfo();
      }
      else {
          $(this).next('label').next('label').removeClass('opacity0');
      }

  });// End


/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    
    // Email Check

    $(".checkEmailCountss").on("focusout",  function() { 

      $('#email').next().addClass('opacity0').next().next('.requiredError').addClass('opacity0');

        var getemail = $('#email').val();
        var popuplatedemail = $('#email').hasClass('popuplatedemail');
        if(popuplatedemail == true)
        { 
          var getLeadId =  $('#email').attr('leadId');  
        }
        else
        { 
          var getLeadId =  $('.thisLeadId').attr('leadid');  
        }

        var getValue = $('#email').val().length;
        
        if ($.trim(getemail).length == 0) {
            $('.emailexists').addClass('opacity0');
            $('#email').next('label').next('label').addClass('opacity0');
        }
        else if (isValidEmailAddress(getemail)) {
            $('#email').next('label').next('label').addClass('opacity0');
            
             $.ajax({
              type: "GET",
              url: "/dashboard/ajaxGetCheckUserEmail?email="+getemail,
              //data: {'email' : email},
              success: function (data) 
              {
                //$('.showloading').show();
                var parsed = '';          
                try{                           
                  parsed = JSON.parse(data);              
                }                 
                catch(e)                
                {                  
                  return false;                  
                }

                if(getLeadId != "" || popuplatedemail == true)
                {
                    var data = {leadId : getLeadId , email : getemail}
                    $.ajax({
                      type: "GET",
                      url: "/dashboard/checkLeadEmail?email="+getemail+"&leadId="+getLeadId,
                      success: function (data) {
                        var parsed2 = '';          
                        try{                           
                          parsed2 = JSON.parse(data);              
                        }                 
                        catch(e)                
                        {                  
                          return false;                  
                        }
                        var getResponse = parsed2[getLeadId].response;
                        if(getResponse == 1)
                        {
                          $('.redCross').addClass('hide');
                          $('.emailexists').html('Email Available!').addClass('green');
                          window.emailexists = false;
                        }
                        else
                        {
                          if(parsed.length > 0)
                            {
                              
                              $('.topBar').trigger('click');
                              $('.redCross').removeClass('hide');
                              $('.redGreen').addClass('hide');
                              $('.emailexists').html('Email Already Exists!').removeClass('opacity0').removeClass('green');
                              window.emailexists = true;
                            }
                            else
                            {
                              $('.redCross').addClass('hide');
                              $('.redGreen').removeClass('hide');
                              $('.emailexists').html('Email Available!').removeClass('opacity0').addClass('green');
                              window.emailexists = false;
                            }  
                        }
                      }
                    });
                }
                else if(parsed.length > 0)
                {
                  
                  $('.topBar').trigger('click');
                  $('.redCross').removeClass('hide');
                  $('.redGreen').addClass('hide');
                  $('.emailexists').html('Email Already Exists!').removeClass('opacity0').removeClass('green');
                }
                else
                {
                  $('.redCross').addClass('hide');
                  $('.redGreen').removeClass('hide');
                  $('.emailexists').html('Email Available!').removeClass('opacity0').addClass('green');

                }        
              
              }
            }); 

        }
        else {
            $('.redCross, .redGreen').addClass('hide');
            $('.emailexists').addClass('opacity0');
            $('#email').next('label').next('label').removeClass('opacity0');
            
        }


    });




/* ----------------------------------------------------*/
    
    // Show additional details container

    $(document).on('click', '.add-addressClick', function () {
        $('.add-address').slideUp();
        $('.addressContainer').slideDown(300);
    });// End

    // Show additional details container

    $(document).on('click', '.btn-nextDetails', function () {
      $('.additional-details').slideDown(500);  
      $('.btn-nextDetails').addClass('hide');
      $('.btn-saveDetails, .btn-bookNow').removeClass('hide');
      $('.next-saveDiv').addClass('one-half-pad-top');
      //$('.add-address').slideUp();
      $('.newLead').removeClass('opened');
      $('#calendar').fullCalendar("destroy");
      $('.btn-cancel').removeClass('gap-right').addClass('triple-gap-right');
      $('.newLead').removeClass('opened');
      $('.additional-details').addClass('opened');
      
    });// End
    

/* ----------------------------------------------------*/
    
    // Calendar Button

    $(document).on('click', '.mainMenu a.calendarLink', function () {
        $('nav.mainMenu li').removeClass('current');
        $(this).closest('li').addClass('current');
        var userisOnDashBoard = $('.contentArea').hasClass('hide');
        if(userisOnDashBoard)
        {}
        else
        {window.location.href = '/dashboard?page=RoomManager';}
        $('.basicInfo').html(window.getBasicInfo);
        $('.additional-details').html(window.getAdditionalInfo);
        setTimeout(function(){ 
            $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
         }, 3000);
        getCustomerByName();
        $('.dashboard-header, .leadsContainer, .leadDeailContainer, .newLead, .leavesContainer, .newLeaveContainer').addClass('hide');

        var getHtml = $('.NewCalendarContainer').html();
        var setHtml = "<div class='NewCalendarContainer full relative'>";
        setHtml += getHtml;
        setHtml += "</div>";
        setHtml += "<div class='hide fixed addBookingPopup'></div>";
        $('.calendarLoad').html(setHtml);
        $('.calendarLoad .bookingHeading').addClass('hide');
        $('.calendarLoad').removeClass('hide');
        $('.newLead').removeClass('inEditMode');
        suggestedDate();
        $('.all-rooms').trigger('click');
        $('.calendarLoad input.newLeadCalendar').addClass('newLeadCalendarBooking').removeClass('newLeadCalendar');
        
        //Setting Calenndar popup for calendar ON Booking Page

        var getTodayDate = moment(); //Get the current date
        getTodayDate.format("YYYY-MM-DD"); 
        $('.newLeadCalendarBooking').daterangepicker({
          singleDatePicker: true,
          startDate: getTodayDate,
          locale: { 
            direction: 'bookingCalendar',
             format: 'YYYY-MM-DD'
          }
        }, function(start, end, label) 
        {
          setTimeout(function(){ 
            var getThisDate = $('.toggleCalendar.newLeadCalendarBooking').val();
            var getWeeklyDate = $('.calendarWeeklyDate').attr('startdate', getThisDate);
            loadWeeklyDates(getThisDate);
            setTimeout(function(){ 
              suggestedDate();
            }, 100);
          }, 300);
          
        });


    });// End

    // Expand Additional Details

    $(document).on('click', '.icMinimize', function () {
        additionalDetailsMinimize();
    });// End

    /* ----------------------------------------------------*/

    // Minimize Additional Details

    $(document).on('click', '.icExpand', function () {
        additionalDetailsExpand();

    });// End

/* ----------------------------------------------------*/
    
    // Showing top headings
    $(document).on('keyup', '.additional-details input', function () {
      if($(this).val().length > 0)
      {
        $(this).prev('span').slideDown(150);
      }    
    });// End

    // -----------------------------------------------------

    // Day selection

    $(document).on('click', '.daySelection a', function () {
        $(this).closest('.daySelection').addClass('setForCalendar')
        $('.daySelection a').removeClass('active');
        $(this).addClass('active');
        $('.selectWeek').removeClass('opacity0');
         
    });

    // Week selection

    $(document).on('click', '.selectWeek a', function () {
        $('.selectWeek a').removeClass('active');
        $(this).addClass('active');
        $('.selectTime').removeClass('opacity0');
    });
    
    // Time selection for Calendar 2

    $(document).on('click', '.timeSelection a', function () {
        $('.timeSelection a').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.durationSelection a', function () {
        $('.durationSelection a').removeClass('active');
        $(this).addClass('active');
    });

    // Time selection for Calendar 2

    $(document).on('click', '.timeSelectionOnly a', function () {
        $('.timeSelectionOnly a').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.durationSelectionOnly a', function () {
        $('.durationSelectionOnly a').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.timeSlots ul li', function () {
      
        $('.timeSlots ul li').removeClass('active');
        $(this).addClass('active');
        var allRoomsBooked = $(this).hasClass('three');
        if(allRoomsBooked)
        {
          $('.timeSlotError').removeClass('opacity0');
          setTimeout(function(){ 
            $('.timeSlotError').addClass('opacity0');
          }, 2000);
        }
    });
    

    $(document).on('click', '.timeSelection_Cl1 a', function () {
        $("#calendar").fullCalendar('removeEvents'); 
        $("#calendar").fullCalendar('addEventSource', calendar1Events);

        $('.timeSelection_Cl1 a').removeClass('active');
        $(this).addClass('active');

        
    });

    $(document).on('click', '.durationSelection_Cl1 a', function () {
        $("#calendar").fullCalendar('removeEvents'); 
        $("#calendar").fullCalendar('addEventSource', calendar1Events);

        $('.durationSelection_Cl1 a').removeClass('active');
        $(this).addClass('active');
        
       
    });


    // Suggested Date Implement

    // Start Setting Dates

    moment.locale('en-custom', {
    week: {
        dow: 1,
        doy: 6 // Adjust the first week of the year, depends on the country. For the US it's 6. For the UK, 4.
        }
    });

    var date = new Date();

    //Setting Days dates
    var i = 0;
    var j = 7;
    var k = 14;
    $('.daySelection a').each(function(){

        $(this).attr('only-date1',moment(date).weekday(i).format('DD'));
        $(this).attr('only-date2',moment(date).weekday(j).format('DD'));
        $(this).attr('only-date3',moment(date).weekday(k).format('DD'));

        $(this).attr('data-date1',moment(date).weekday(i).format('DD MMMM'));
        $(this).attr('data-date2',moment(date).weekday(j).format('DD MMMM'));
        $(this).attr('data-date3',moment(date).weekday(k).format('DD MMMM'));

        // Setting full dates
        $(this).attr('data-fullDate1',moment(date).weekday(i).format('YYYY-MM-DD'));
        $(this).attr('data-fullDate2',moment(date).weekday(j).format('YYYY-MM-DD'));
        $(this).attr('data-fullDate3',moment(date).weekday(k).format('YYYY-MM-DD'));

        i++;
        j++;
        k++;
    });

    // End Setting Dates


    // Setting dates for calendar load

var date = new Date();
var getMonth = date.getMonth() + 1;
var getMonthNumber = date.getMonth() + 1;
var getYear = date.getFullYear();
window.startingMonth = getMonth;
window.startingYear = getYear;

SetLeaveContent(getMonth,getYear);



// Start Set Years for Dropdown

var CurrentYear = getYear;
CurrentYear--;
var SetYearDropdown = "";
for (var i = 0; i < 4; i++) {
    
    SetYearDropdown += "<span value='"+CurrentYear+"' year='"+CurrentYear+"'>"+CurrentYear+"</span>";
    CurrentYear++
  }  

$('.yearCalendarDropdown, .yearDropdown').html(SetYearDropdown);

$('.calendarLeaveYear span, .leaveYear span').attr('year',getYear);
$('.calendarLeaveYear span, .leaveYear span').text(getYear);

// End Set Years for Dropdown



// Get Current Month Dates
var m_names = ['January', 'February', 'March', 
               'April', 'May', 'June', 'July', 
               'August', 'September', 'October', 'November', 'December'];
//month name

var getMonth = m_names[date.getMonth()];


// Last day of month
var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

var getlastDay = lastDay.getDate();

function dateSelected(getMonth, getlastDay)
{
    var lastDay = '';
    if(getlastDay == '30')
    {
        lastDay = '30<span class="subTopText">th</span>';
    }
    else if(getlastDay == '31')
    {
        lastDay = '31<span class="subTopText">st</span>';
    }
    else
    {
        lastDay = '29<span class="subTopText">th</span>';
    }
    var setHtml = getMonth + "1<span class='subTopText'>st</span> - " +  getMonth + " " + lastDay;
    window.thisMonth = setHtml;
    $('.dateSelected').html(setHtml);
}

setTimeout(function(){ 

    dateSelected(getMonth,getlastDay);
    
    // filter calendar set dates
    $('.calendarClose .calendarLeaveMonth span , .leadListCalendar .calendarLeaveMonth span, .leaveMonth span').html(getMonth);
    $('.calendarClose .calendarLeaveMonth span , .leadListCalendar .calendarLeaveMonth span, .leaveMonth span').attr('month', getMonthNumber);
    $('.calendarClose .calendarLeaveYear span , .leadListCalendar .calendarLeaveYear span, .leaveYear span').html(getYear);
    $('.calendarClose .calendarLeaveYear span , .leadListCalendar .calendarLeaveYear span, .leaveYear span').attr('year', getYear);


}, 300);

setTimeout(function(){ 
    window.getLeadPopup = $('#closeLead').html(); 
}, 400);

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Get Today's Date

function getSuffix(getDay)
{
    var suffix = ''
    if(getDay == 1 || getDay == 21 || getDay == 31)
    {
        suffix = 'st'
    }
    else if(getDay == 2 || getDay == 22)
    {
        suffix = 'nd'   
    }
    else if(getDay == 3 || getDay == 23)
    {
        suffix = 'rd' 
    }
    else
    {
        suffix = 'th'    
    }
    return '<span class="subTopText">' + suffix + '</span>';
}

var getDays = date.getDate();
var getSuffix = getSuffix(getDays);
var todaysDate = getDays + getSuffix + " " + getMonth;
window.todayDate = todaysDate


// Get Yesterday

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/


function getSuffix2(getDay)
{
    var suffix = ''
    if(getDay == 1 || getDay == 21 || getDay == 31)
    {
        suffix = 'st'
    }
    else if(getDay == 2 || getDay == 22)
    {
        suffix = 'nd'   
    }
    else if(getDay == 3 || getDay == 23)
    {
        suffix = 'rd' 
    }
    else
    {
        suffix = 'th'    
    }
    return '<span class="subTopText">' + suffix + '</span>';
}

var yesterday = new Date();
yesterday.setDate(yesterday.getDate() - 1);
var getYesterDay = yesterday.getDate();
var getSuffix2 = getSuffix2(getYesterDay);

var getMonth2 = yesterday.getMonth() + 1;
var getMonth2 = m_names[yesterday.getMonth()];

var todaysDate2 = getYesterDay + getSuffix2 + " " + getMonth2;
window.todayDate2 = todaysDate2

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Get Weekly Dates

//Setting Days dates
    
    setFirstDate = '';
    setLastDate = '';

    for (var a = 0; a < 7; a++) {

        // Setting Week Start And Week End Dates
        //var getCurrentDate = moment(date).weekday(a).format('DD');
        var getCurrentDate = moment(date).weekday(a).format('D');
        var getCurrentMonth = moment(date).weekday(a).format('MMMM');
        var dateSuffix = ''
        if(getCurrentDate == 1 || getCurrentDate == 21 || getCurrentDate == 31)
        {
            dateSuffix = 'st'
        }
        else if(getCurrentDate == 2 || getCurrentDate == 22)
        {
            dateSuffix = 'nd'   
        }
        else if(getCurrentDate == 3 || getCurrentDate == 23)
        {
            dateSuffix = 'rd' 
        }
        else
        {
            dateSuffix = 'th'    
        }
        dateSuffix = '<span class="subTopText">' + dateSuffix + '</span>';
        if(a == 0)
        {
            setFirstDate = getCurrentMonth  + ' ' + getCurrentDate + dateSuffix 
        }
        else if(a == 6)
        {
            setLastDate = getCurrentMonth  + ' ' + getCurrentDate + dateSuffix
        }
    }

    window.weeklyDate = setFirstDate + ' - ' + setLastDate;

/*---------------------------------------------*/



/*------------------New Calendar Js ---------------------*/
/*------------------New Calendar Js ---------------------*/
/*------------------New Calendar Js ---------------------*/

// Rings filter

$(document).on('click','.bookingRooms a', function (e) {

    //showMainCalendarLoading();
    var getValue = $(this).attr('value');
    $('.bookingRooms a').removeClass('active');
    $(this).addClass('active');
  
    if(getValue == "All Rooms")
    {
      $('.roomFour, .roomThree, .roomTwo, .roomOne').removeClass('hide');
      $('.roomsContainer').removeClass('scrolled');
    }
    else if(getValue == "Room 1")
    {
      //$('.daysSlider label.room3 img').trigger('click');

      $('.roomFour, .roomThree, .roomTwo').addClass('hide');
      $('.roomOne').removeClass('hide');
      $('.roomsContainer').removeClass('scrolled');
    }
    else if(getValue == "Room 2")
    {
      $('.roomFour, .roomThree, .roomOne').addClass('hide');
      $('.roomTwo').removeClass('hide');
      $('.roomsContainer').removeClass('scrolled');
      //$('.daysSlider label.room3 img').trigger('click');
    }
    else if(getValue == "Room 3")
    {
      $('.roomFour, .roomTwo, .roomOne').addClass('hide');
      $('.roomThree').removeClass('hide');
       $('.roomsContainer').addClass('scrolled');
      //$('.daysSlider label.room2 img').trigger('click');
    }
    else
    {
      $('.roomThree, .roomTwo, .roomOne').addClass('hide');
      $('.roomFour').removeClass('hide');
       $('.roomsContainer').addClass('scrolled');
      //$('.daysSlider label.room2 img').trigger('click');
    }
});

/*====================================================*/

// New Calendar Dates Scroll Filters
$(document).on('click','.arrowNext, .arrowPrev', function (e) {
  $('.showMessage').removeClass('topShow');
  $('.daysContent').removeClass('agentOnLeave').removeClass('pastdate'); 

  if($(this).attr('date-scroll') == "next")
  {
    var getThisDate = $('.weeklyDates span').attr('nextweekdate');
    loadWeeklyDates(getThisDate);
  }
  else
  {
    var getThisDate = $('.weeklyDates span').attr('previousweekdate');
    loadWeeklyDates(getThisDate);
  }
  // Reset Rooms filter
  $('.bookingRooms a').removeClass('active');
  $('.all-rooms').addClass('active');
   
  // Calling load calendar
  suggestedDate();
});


/* ----------------------------------------------------*/

//Setting Calenndar popup for calendar ON New Lead Page

$(function() {
  var getTodayDate = moment(); //Get the current date
  getTodayDate.format("YYYY-MM-DD"); 
  $('input.newLeadCalendar').daterangepicker({
    singleDatePicker: true,
    startDate: getTodayDate,
    locale: { 
      direction: 'bookingCalendar',
       format: 'YYYY-MM-DD'
    }
  }, function(start, end, label) 
  {
    setTimeout(function(){ 
      var getThisDate = $('.toggleCalendar').val();
      var getWeeklyDate = $('.calendarWeeklyDate').attr('startdate', getThisDate);
      loadWeeklyDates(getThisDate);
      setTimeout(function(){ 
        suggestedDate();
      }, 100);
    }, 300);
    
  });
});


/*====================================================*/
// New Calendar Dates Scroll Filter
$(document).on('click','.daysSlider label.room2', function (e) {
    $(this).closest('.roomsContainer').addClass('scrolled');
    var getDay = $(this).closest('.daysCalendar').attr('titleDay');
    $('.' + getDay).find('.roomsContainer').addClass('scrolled');
});

/*====================================================*/

// New Calendar Dates Scroll Filter

$(document).on('click','.daysSlider label.room3', function (e) {
    $(this).closest('.roomsContainer').removeClass('scrolled');
    var getDay = $(this).closest('.daysCalendar').attr('titleDay');
    $('.' + getDay).find('.roomsContainer').removeClass('scrolled');
});

/*====================================================*/

// New Calendar Dates Scroll Filter

$(document).on('click','.borderBottom i.icon-downarrow, .customerName, .salesRepName', function (e) {
    $(".pickAgentresult").html(' ').addClass('hide');
    $(".pickAgentresult2").removeClass('hide');
    if($(this).closest('.borderBottom').find('.newbookingdropdown').hasClass('hide'))
    {
      $('.newbookingdropdown').addClass('hide');
      $(this).closest('.borderBottom').find('.newbookingdropdown').removeClass('hide');
      $(this).closest('.borderBottom').find('input').focus();
    }
    else
    {
      $(this).closest('.borderBottom').find('.newbookingdropdown').addClass('hide');
    }
    
});

/*====================================================*/

// Add new Booking popup

$(document).on('click','.addBookingLink', function (e) {

    window.userColor = '';
    var editBooking = $('.loadnewCalendarContent').hasClass('editable');
    $('.rightCol').addClass('overflowHidden');
    var el = $(this);
    $('#email').next().addClass('opacity0').next('.requiredError').addClass('opacity0');
    window.validState = true;

    var checkIfAppointment = $(this).closest('.calendarLoad').hasClass('full');
    //console.log(checkIfAppointment);
    if(checkIfAppointment)
    {
      window.AppointmentType = 1;
    }
    else
    {
      window.AppointmentType = 0;
      validation();
    }

    //$('section.rightCol').addClass('hidden');
    
    
    var validationCheck = el.closest('.calendarLoad').hasClass('full');
    if(validationCheck == false)
    {
        if(window.validState == false )
        { return false }
    }
    
    

    $('.addBookingLink').removeClass('thisClicked');
    $('.labelContainer').removeClass('thisLabelClicked');
    $(this).addClass('thisClicked');
    $(this).closest('.labelContainer').addClass('thisLabelClicked');
    var getRoomNumber = $(this).closest('.labelContainer').attr('roomnumber');
    var timeStart = $(this).attr('bookingstart');

    var getTimeAllowed = checkTimeAllowed(timeStart, getRoomNumber, el);
    window.getTimeAllowed = getTimeAllowed;
    var p = $(this)
    var offset = p.offset();
    var getOffsetTop = offset.top;
    var getOffsetLeft = offset.left;
    getOffsetTop = getOffsetTop + 1;
    getOffsetLeft = getOffsetLeft + 15;
    
    var getBudget = $('.dropdown.budget').find('a.selected-text').attr('value');
    var getTime = $(this).closest('.daysContentSlider').attr('time');
    var getTime2 = $(this).closest('.daysContentSlider').attr('startingtime');
    var getDate = $(this).closest('.daysContent').attr('fulldate');
    
    var getMonth = moment(getDate).format('MMM');
    var getYear = moment(getDate).format('YY');
    var getDay = moment(getDate).format('DD');
    var setDate = getDay + '-' + getMonth + '-' + getYear + '  Room ';

    var getTimeSlots = getTimeSlot(getTime);
    var appointmentTypes = getAppointmentTypes();
    var salesRep = getSalesRep();
    $('#bookingDate').attr('timeSlot', getTimeSlots);
    $('#bookingDate').attr('timeSlotFull', getTime);
    $('#bookingDate').attr('StartingTimeOnly', getTime2);

    var getName = $('#first_name').val() + ' ' + $('#last_name').val();
    var getSalesRepName = $('#assign_us_Dropdown').html();
    
    var getProductSC = $('.dropdown.product').find('a.selected-text').attr('shortcode');
    
    var getUserSC = $('.dropdown.assignToDiv').find('a.selected-text').attr('shortcode');
    var bookingTimeStart = $('.addBookingLink.thisClicked').attr('bookingstart');
    if(bookingTimeStart == "0")
      {bookingTimeStart = "00"}
    var StartingHour = $('.addBookingLink.thisClicked').closest('.daysContentSlider').attr('startingtime');


    var setDropdown = '<ul class="dropdown d-i-b pull-left relative durationTime z-index9">';
          setDropdown += '<li><a href="javascript:;" class="selected-text d-b"getDuration=".25" getDuration2="15" value="15 minutes"><span id="durationTime" class="d-i-b" style="display: inline-block;">15 minutes</span><i class="icon-downarrow fs-9 pull-right d-i-b "></i></a></li>';
         setDropdown += '<li><ul class="dropdownOptions bg-white absolute full-width" style="display: none;">';
          var getMinuteList = getMinutesList(getTimeAllowed);
          setDropdown += getMinuteList;
          setDropdown += '</ul></li>';
        setDropdown += '</ul>';

    var setHtml = "";
    var setHtml = '<div class="addBookingContainer roomsContainer">';
      setHtml += '<div class="roomBooking">';
        setHtml += '<div class="fs-11 headBar" style="background-color:'+window.userColor+'"><p class="newApp">New appointment</p><p class="bookingmade hide"><span class="ellipsis">'+getName+'</span><span>'+getUserSC+'</span></p><div class="appDetails hide"></div></div>';
          

          setHtml += '<div class="full align-left half-pad-left lh-16 fs-11 one-pad-top relative contentarea pad-bottom">';

              if(window.AppointmentType == 1) // Room Manager
              {
                setHtml += '<div class="appointmentTypes half-pad-top">' + appointmentTypes + '</div>';
                setHtml += '<div class="appointmentTypesOther half-pad-top hide">';
                  setHtml += "<div class='half-pad-top borderBottom hideOnCalenar'><span class='subheading'>Duration*</span>" +  setDropdown + "</div>";
                  setHtml += "<div class='half-pad-top hideOnCalenar otherProduct half-gap-right relative'> <span class='subheading'>Product Name*</span><input type='text' placeholder='Enter Product Name' id='otherProductInput' class='full half-gap-top fs-10 lh-16 half-pad-right half-pad-left otherProductInput' value='' /></div>";
                  setHtml += "<div class='half-pad-top hideOnCalenar half-gap-right'><a href='JavaScript:;' class='addproduct bg-green color-white fs-10 full align-center gap-top lh-22'>Add Product</a></div>";
                setHtml += '</div>';
                setHtml += '<div class="newBookingDetail hide full">';   
                // Time Date Room
                setHtml += '<p class="half-pad-top half-pad-bottom hideOnCalenar"> <i class="icon-leave fs-12" style="color:'+window.userColor +'"></i> ' + setDate + getRoomNumber + '</p>';  
                // Duration 
                setHtml += '<div class="half-pad-top half-pad-bottom borderBottom newBookingDuration hideOnCalenar"> <span class="subheading" value="90">Duration</span> 1.5 Hours</div>';  
                // Customer Name Other

                setHtml += '<div class="half-pad-top borderBottom hideOnCalenar customerNameOther half-gap-right hide"> <span class="subheading ">Name</span> <input type="text" placeholder="Name" class="customerName2 full half-gap-bottom half-gap-top fs-10 lh-14"/></div>';
                // Customer Name
                if(editBooking == true)
                {
                  // Customer Name
                  setHtml += '<div class="half-pad-top borderBottom hideOnCalenar customerNamedefault"> <span class="subheading hide">Name</span> <span class="customerName  display-block ellipsis" value="'+window.customerName+'">'+window.customerName+'</span> <i class="icon-downarrow fs-12 pull-right d-i-b "></i><div id="customerRepSelect" class="hide newbookingdropdown"><input type="text" placeholder="Search" id="newbookingdropdown"/><div class="customerresult"></div></div></div>'; 
                  // Sales Rep
                  setHtml += '<div class="half-pad-top half-pad-bottom borderBottom hideOnCalenar"> <span class="subheading hide">Sales Rep</span><span class="salesRepName display-block" userid="'+window.ownerID+'" value="'+window.ownerName+'">'+window.ownerName+'</span><i class="icon-downarrow fs-12 pull-right d-i-b "></i><div id="salesRepSelect" class="hide newbookingdropdown"><input type="text" placeholder="Search" id="newbookingdropdown"/><div class="pickAgentresult hide"></div><div class="pickAgentresult2"></div></div></div>';  
                }
                else
                {
                  // Customer Name
                  setHtml += '<div class="half-pad-top borderBottom hideOnCalenar customerNamedefault"> <span class="subheading hide">Name</span> <span class="customerName  display-block ellipsis" value="">Name</span> <i class="icon-downarrow fs-12 pull-right d-i-b "></i><div id="customerRepSelect" class="hide newbookingdropdown"><input type="text" placeholder="Search" id="newbookingdropdown"/><div class="customerresult"></div></div></div>'; 
                  // Sales Rep
                  setHtml += '<div class="half-pad-top half-pad-bottom borderBottom hideOnCalenar"> <span class="subheading hide">Sales Rep</span><span class="salesRepName display-block" value="">Sales Rep</span><i class="icon-downarrow fs-12 pull-right d-i-b "></i><div id="salesRepSelect" class="hide newbookingdropdown"><input type="text" placeholder="Search" id="newbookingdropdown"/><div class="pickAgentresult hide"></div><div class="pickAgentresult2"></div></div></div>';
                }
                
                
              }
              else // New Lead
              {
                setHtml += '<div class="appointmentTypes half-pad-top hide">' + appointmentTypes + '</div>';
                setHtml += '<div class="newBookingDetail ">';
                // Time Date Room
                setHtml += '<p class="half-pad-top half-pad-bottom hideOnCalenar"> <i class="icon-leave fs-12" style="color:'+window.userColor +'"></i> ' + setDate + getRoomNumber + '</p>';
                setHtml += "<div class='half-pad-top borderBottom hideOnCalenar'> <span class='subheading'>Duration</span>" +  setDropdown + "</div>";
                // Customer Name
                setHtml += '<div class="half-pad-top borderBottom hideOnCalenar"> <span class="subheading">Name</span> <p class="two-pad-bottom"><i class="icon-user fs-11" style="color:'+window.userColor+'"></i> <span class="d-i-b">'+getName+'</span></p></div>';
                // Sales Rep
                setHtml += '<div class="half-pad-top borderBottom hideOnCalenar"> <span class="subheading">Sales Rep</span> <p class="two-pad-bottom"><i class="icon-user fs-11" style="color:'+window.userColor+'"></i> <span class="d-i-b">'+getSalesRepName+'</span></p></div>';
              }
              
              // Product Name
              setHtml += '<p class="productShortCode hide"><i class="icon-diamond" style="color:'+window.userColor+'"></i> <span class="ellipsis d-i-b half-pad-left">'+getProductSC+'</span></p>';
              
              //setHtml += '<p><i class="icon-dollar fs-11 " style="color:'+window.userColor+'"></i> <span class=" d-i-b half-pad-left">'+getBudget+'</span></p>';
              
              //setHtml += '<p class="bookingTiming"><i class="icon-clock fs-12 " style="color:'+window.userColor+'"></i> <span class=" d-i-b half-pad-left">Starting at ' +StartingHour+':'+bookingTimeStart+'</span></p>';
              //setHtml += '<p class="bookingTiming pull-left"><i class="icon-clock fs-12 " style="color:'+window.userColor+'"></i></p>';
              //setHtml += setDropdown;

              setHtml += '<p class="full"><label class="saveBookingError hide">Please fill all fields!</label><a class="savePopupBooking" href="JavaScript:;">Save</a></p>';
            setHtml += '</div>';
          setHtml += '<div class="transparentBG absolute" style="background-color:'+window.userColor+'"></div>';

        setHtml += '</div>';
      setHtml += '</div>';
    setHtml += '</div>';


    //var container = "<div class='tempContainer fixed' style='top:"+getOffsetTop+"px ; left:"+getOffsetLeft+"px'>" + setHtml + "</div>";
    var container = "<div class='popupBackground'></div><div class='tempContainer fixed' style='top:"+getOffsetTop+"px ; left:"+getOffsetLeft+"px'>" + setHtml + "</div>";
    //$(this).after(container);
    $('.addBookingPopup').html(container).removeClass('hide');
    //$('.pickAgentresult2').html(window.pickAgentresult2);


    return false;


});


/*====================================================*/

// Save new Booking popup

$(document).on('click','.savePopupBooking', function (e) {
    $('.rightCol').removeClass('overflowHidden');
    //$('.saveNewBookingForNewLead').removeClass('hide');
    $('.cancelNewBooking').removeClass('withoutSave');
    var el = $(this);
    if(window.other == true)
    {
      var customerName = el.closest('.calendarLoad .addBookingPopup .newBookingDetail').find('.customerName2').attr('value');
    }
    else
    {
      var customerName = el.closest('.calendarLoad .addBookingPopup .newBookingDetail').find('.customerName').attr('value');  
    }
    
    var pickUpAgent = el.closest('.calendarLoad .newBookingDetail').find('.salesRepName').attr('value');
    var pickUpAgentId = el.closest('.calendarLoad .newBookingDetail').find('.salesRepName').attr('userId');
    $('#assign_us_Dropdown').closest('a.selected-text').attr('value',pickUpAgent);
    $('#assign_us_Dropdown').html(pickUpAgent);
    $(".assignToDiv a.selected-text").attr("assigneid", pickUpAgentId);
    
    if(window.AppointmentType == 1) // Room Manager
      {
        var getDuration = $('.calendarLoad .addBookingPopup .newBookingDuration .subheading').attr('value');
        if(customerName == "" || pickUpAgent == "")
        {

          $('.saveBookingError').removeClass('hide');
          // setTimeout(function(){ 
          //     $('.saveBookingError').addClass('hide');
          // }, 3000);
          return false;
        }
        else
        {
          
        }
        // Get shortcode 

        var res = pickUpAgent.split(" ");
        var firstNameWord = res[0];
        var middleNameWord = res[1];
        var lastsNameWord = res[2];
        var firstLetterFirstName = firstNameWord.charAt(0);
        var middleLetterFirstName = '';
        var lastLetterFirstName = '';

        if(middleNameWord != null){ middleLetterFirstName = middleNameWord.charAt(0); }
        if(lastsNameWord != null){ lastLetterFirstName = lastsNameWord.charAt(0); }

        if(lastsNameWord != null)
        {
          var shortCode = firstLetterFirstName+middleLetterFirstName+lastLetterFirstName;
        }
        else
        {
          var shortCode = firstLetterFirstName+middleLetterFirstName;
        }
      }
      else
      {
        var getDuration = $('.addBookingPopup .dropdown.durationTime').find('a.selected-text').attr('getduration2');
      }

    $('.roomBooking.newlyAdded').remove();
    var selectHtml = $('.addBookingPopup .addBookingContainer').html();
    var getDayName = $('.addBookingLink.thisClicked').closest('.daysCalendar').attr('dayname');
    var getDateNumber = $('.addBookingLink.thisClicked').closest('.daysContent').attr('datenumber');
    var getSelectedDate = $('.addBookingLink.thisClicked').closest('.daysContent').attr('fulldate');
    //var getDuration = $('.dropdown.durationTime').find('a.selected-text').attr('getduration');
    var bookingTimeStart = $('.addBookingLink.thisClicked').attr('bookingstart');
    var StartingTimeOnly = $('#bookingDate').attr('StartingTimeOnly');
    bookingTimeDuration(getDuration, bookingTimeStart, StartingTimeOnly);
    
    var selectedProduct = el.closest('.roomBooking').find('.dropdown.appointmentType').find('a.selected-text').attr('shortCode');
  
    var setHeight = '';
    if(getDuration == "15")
    {
      setHeight = '16px';
    }
    else if(getDuration == "30")
    {
      setHeight = '33px';
    }
    else if(getDuration == "45")
    {
      setHeight = '49px';
    }
    else if(getDuration == "60")
    {
      setHeight = '64px';
    }
    else if(getDuration == "75")
    {
      setHeight = '81px';
    }
    else if(getDuration == "90")
    {
      setHeight = '98px';
    }

    var setTop = ''; 
    if(bookingTimeStart == "0")
    {
      setTop = '0px';
    }
    else if(bookingTimeStart == "15")
    {
      setTop = '15px';
    }
    else if(bookingTimeStart == "30")
    {
      setTop = '31px';
    }
    else if(bookingTimeStart == "45")
    {
      setTop = '47px';
    }

    $('#bookingDate').attr('dayName', getDayName);
    $('#bookingDate').attr('datenumber', getDateNumber);
    $('#bookingDate').attr('customerName', customerName);
    $('#bookingDate').attr('salesRepName', pickUpAgent);

    $('.addBookingLink.thisClicked').closest('label').append(selectHtml);
    $('.thisLabelClicked .roomBooking:last-child').css('height',setHeight).addClass('newlyAdded');
    $('.thisLabelClicked .roomBooking:last-child').css('top',setTop);
    $('.thisLabelClicked .roomBooking:last-child').find('.transparentBG').css('height',setHeight);
    $('.thisLabelClicked .roomBooking:last-child').find('.bookingTiming, .durationTime, p.full').addClass('hide');

    if(window.other == true)
    {
      $('.thisLabelClicked .roomBooking:last-child .productShortCode i').attr('class', '').addClass('icon-other');
      $('.thisLabelClicked .roomBooking:last-child .productShortCode span').html(window.otherProduct);
      $("#productDropdown").text(window.otherProduct);
      $('#productDropdown').closest('a.selected-text').attr('value', window.otherProduct); 


    }
    else if(window.AppointmentType == 1)
    {
      $('.thisLabelClicked .roomBooking:last-child .productShortCode i').attr('class', '').addClass(window.productIcon);
      $("#productDropdown").text(window.mainProduct);
      $('#productDropdown').closest('a.selected-text').attr('value', window.mainProduct); 
      
        
    }

    $('.addBookingPopup').html('');
    $('.addBookingPopup').addClass('hide');
    $('#bookingDate').attr('durationTime', getDuration);
    $('#bookingDate').attr('bookingStart', bookingTimeStart);

    // Apply new booking made
    if(window.AppointmentType == 1)
    {
      $('.appointmentTypes').addClass('hide');
      $('.newBookingDetail').removeClass('hide');
      var setCustomerName = '<span class="ellipsis">'+customerName+'</span><span>'+shortCode+'</span>';
      $('.bookingmade').html(setCustomerName);  
       
    }
    else
    {
      
    }
    $('.appDetails,.newApp, .hideOnCalenar').addClass('hide');
    $('.next-saveDiv').removeClass('hide');
    $('.bookNowDiv').addClass('hide');
    additionalDetailsExpand();
    $('.btn-bookNow').addClass('hide');
    $('.savedBooking').removeClass('hide');
    $('.bookingViewIcon').addClass('hide');
    
    $('.bookingmade, .productShortCode').removeClass('hide');
    //$('.next-options').removeClass('hide');
    geDateValues(getSelectedDate);    
    //$('.NewCalendarContainer').addClass('hide');
    $('.loadnewCalendarContent').removeClass('editable');
    $('.thisLabelClicked').closest('.NewCalendarContainer').find('.calendarHead .saveNewBooking').trigger('click');
    $('.thisLeadId').attr('leadid','');
    $('#appointmentId').attr('appointmentid','0');
    $('#customerId').attr('customerid','0');
    if(window.AppointmentType == 1)
    {

      //$('.saveNewBooking').trigger('click'); 
    }
    else
    {
      //$('.saveNewBooking').trigger('click');
    }
    
    
});


function bookingTimeDuration(getDuration, bookingTimeStart, StartingTimeOnly)
  {
    var duration = parseInt(getDuration);
    var bookingTime = parseInt(bookingTimeStart);
    var StartingTime = parseInt(StartingTimeOnly);
    var TotalDuration = bookingTime + duration;
    var setTime = StartingTimeOnly + ":00";
    // Starting Time

    var StartTime =  moment.utc(setTime,'hh:mm').add(bookingTime,'minutes').format('hh:mm');
    var EndTime =  moment.utc(setTime,'hh:mm').add(TotalDuration,'minutes').format('hh:mm');
    var getHour = EndTime.substr(0, 2);
    getHour = parseInt(getHour);

    // Starting Am Pm
    var startTimeAmPm = "PM";
    if(StartingTime < 12 && StartingTime > 7)
    {
      startTimeAmPm = "AM";
    }

    // Ending Am Pm
    var endingTimeAmPm = "PM";
    if(getHour < 12 && getHour > 7)
    {
      endingTimeAmPm = "AM";
    }
   
    
    var setTime = StartTime + ' ' + startTimeAmPm + ' - ' + EndTime + ' ' + endingTimeAmPm;
    $('#bookingDate').attr('updatedTime', setTime);
  
  }
// Check how many bookings are left there
  
  function checkTimeAllowed(timeStart, getRoomNumber, el)
  {
    
    var positionTop = $('a.addBookingLink.thisClicked').closest('.labelContainer').find('.roomBooking:not(.roomBooking.editable)').attr('topPosition');
    var positionTop2 = $('a.addBookingLink.thisClicked').closest('.daysContentSlider').next('.daysContentSlider').find('.labelContainer[roomnumber="'+ getRoomNumber +'"]').find('.roomBooking:not(.roomBooking.editable)').attr('topPosition');
    var getRoomsLength = $('a.addBookingLink.thisClicked').closest('.labelContainer').find('.roomBooking:not(.roomBooking.editable)').length;
    if(getRoomsLength > 1)
    {
      positionTopSub1 = $('a.addBookingLink.thisClicked').closest('.labelContainer').find('.roomBooking').next('.roomBooking:not(.roomBooking.editable)').attr('topPosition');
      positionTopSub2 = $('a.addBookingLink.thisClicked').closest('.labelContainer').find('.roomBooking').next('.roomBooking:not(.roomBooking.editable)').next('.roomBooking').attr('topPosition');
      
      // If popup starts at 15
      if(timeStart == '15')
      {
        if(positionTopSub1 == '31px')
        {
          checkRoom = 1;
          return checkRoom;
        }
        else if( positionTopSub1 == '47px')
        {
          checkRoom = 2;
          return checkRoom;
        }
      }

      // If popup starts at 30
      if(timeStart == '30')
      {
        if( positionTopSub1 == '47px')
        {
          checkRoom = 1;
          return checkRoom;
        }
      }

      if(timeStart == '15' || timeStart == '30' || timeStart == '45')
      {

      }

    }

    var timeStartFrom = 0
    var checkRoom = 0

    if(positionTop2 == undefined || positionTop2 == null)
    {
      if(positionTop == undefined || positionTop == null)
      {
        checkRoom = 6;
        return checkRoom;
      }
      if(positionTop == "0px")
      {
        checkRoom = 6;
        return checkRoom;
      }
    }

    if(timeStart == '0')
    {timeStartFrom = 0}
    else if(timeStart == '15')
    {timeStartFrom = 1}
    else if(timeStart == '30')
    {timeStartFrom = 2}
    else
    {timeStartFrom = 3}


    if(positionTop == '15px')
    { 
      checkRoom++;
    }
    else if(positionTop == '31px')
    { 
      checkRoom++;
      checkRoom++;
    }
    else if(positionTop == '47px')
    { 
      checkRoom++;
      checkRoom++;
      checkRoom++;
    }
    else 
    {
      checkRoom = 0;
    }

    var getRoom = parseInt(timeStartFrom);
    
    
    if(checkRoom == 0)
    {
      if(timeStart == '0')
      {checkRoom = 4;}
      else if(timeStart == '15')
      {checkRoom = 3;}
      else if(timeStart == '30')
      {checkRoom = 2;}
      else
      {checkRoom = 1;}
    }

    if(positionTop2 == '15px')
    { 
      checkRoom+= 1;
    }
    else if(positionTop2 == '31px')
    { 
      checkRoom+= 2;
    }
    else if(positionTop2 == '47px')
    { 
      checkRoom+= 3;
    }
    positionTop

    if(positionTop2 == undefined || positionTop2 == null)
    {
      checkRoom = checkRoom - getRoom;
      if(positionTop == undefined || positionTop == null)
      {
        checkRoom = 6;
      }
    }

    return checkRoom;
    
  }

  function getMinutesList(getTimeAllowed)
  {

    var setDropdown = "";
    
    if(getTimeAllowed == 1)
    {
        setDropdown += '<li><a href="javascript:;" getDuration="0.25" getDuration2="15" value="15 minutes">15 minutes</a></li>';
    }
    else if(getTimeAllowed == 2)
    {
        setDropdown += '<li><a href="javascript:;" getduration="0.25" getDuration2="15" value="15 minutes">15 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.5" getDuration2="30" value="30 minutes">30 minutes</a></li>';
    }
    else if(getTimeAllowed == 3)
    {
        setDropdown += '<li><a href="javascript:;" getduration="0.25" getDuration2="15" value="15 minutes">15 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.5" getDuration2="30" value="30 minutes">30 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.75" getDuration2="45" value="45 minutes">45 minutes</a></li>';
    }
    else if(getTimeAllowed == 4)
    {
        setDropdown += '<li><a href="javascript:;" getduration="0.25" getDuration2="15" value="15 minutes">15 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.5" getDuration2="30" value="30 minutes">30 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.75" getDuration2="45" value="45 minutes">45 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="1" getDuration2="60" value="60 minutes">60 minutes</a></li>';
    }
    else if(getTimeAllowed == 5)
    {
        setDropdown += '<li><a href="javascript:;" getduration="0.25" getDuration2="15" value="15 minutes">15 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.5" getDuration2="30" value="30 minutes">30 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.75" getDuration2="45" value="45 minutes">45 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="1" getDuration2="60" value="60 minutes">60 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="1.25" getDuration2="75" value="75 minutes">75 minutes</a></li>';
    }
    else
    {
        setDropdown += '<li><a href="javascript:;" getduration="0.25" getDuration2="15" value="15 minutes">15 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.5" getDuration2="30" value="30 minutes">30 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="0.75" getDuration2="45" value="45 minutes">45 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="1" getDuration2="60" value="60 minutes">60 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="1.25" getDuration2="75" value="75 minutes">75 minutes</a></li>';
        setDropdown += '<li><a href="javascript:;" getduration="1.5" getDuration2="90" value="90 minutes">90 minutes</a></li>';
    }
    return setDropdown;
  }
            
  function getAppointmentTypes()
  {

      var setDropdown = "";
      var setDropdown = '<ul class="dropdown d-i-b pull-left relative appointmentType z-index9 bookingPopup">';
            setDropdown += '<li><a href="javascript:;" class="selected-text d-b" getDuration="60" value=""><span id="appointmentType" class="d-i-b" style="display: inline-block;">Select type</span><i class="icon-downarrow fs-12 pull-right d-i-b "></i></a></li>';
            setDropdown += '<li><ul class="dropdownOptions bg-white absolute full-width" style="display: none;">';

                setDropdown += '<li><a href="javascript:;" getDuration="1.5" value="Engagement Ring" shortcode="ER"> <i class="icon-engRing fs-12" icon="icon-engRing"></i> Engagement Ring<span class="hourSlot">1.5 hours</span></a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="1" value="Wedding Band" shortcode="WB"> <i class="icon-weddingBrand fs-12" icon="icon-weddingBrand"></i> Wedding Band<span class="hourSlot">1 hour</span></a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="0.5" value="Resize" shortcode="R"><i class="icon-resize fs-12" icon="icon-resize"></i> Resize<span class="hourSlot">0.5 hour</span></a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="0.5" value="Ring Collection" shortcode="RC"><i class="icon-dressRings fs-12" icon="icon-dressRings"></i> Ring Collection<span class="hourSlot">0.5 hour</span></a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="0.5" value="Ring Drop Off" shortcode="RDO"><i class="icon-drop-off fs-12" icon="icon-drop-off"></i> Ring Drop Off<span class="hourSlot">0.5 hour</span></a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="0" value="other" shortcode=""><i class="icon-other fs-12 "></i> Other <span class="hourSlot"></span></a></li>';

              setDropdown += '</ul></li>';
        setDropdown += '</ul>';
        return setDropdown;
  }
  function getSalesRep()
  {
    var setDropdown = "";
      var setDropdown = '<ul class="dropdown d-i-b pull-left relative SalesRep z-index9 bookingPopup">';
            setDropdown += '<li><a href="javascript:;" class="selected-text d-b" getDuration="60" value=""><span id="SalesRep" class="d-i-b" style="display: inline-block;">Select Type</span><i class="icon-downarrow fs-12 pull-right d-i-b "></i></a></li>';
            setDropdown += '<li><ul class="dropdownOptions bg-white absolute full-width" style="display: none;">';

                setDropdown += '<li><a href="javascript:;" getDuration="1.5" value="Engagement Ring" shortcode="ER"> <i class="icon-diamond fs-12"></i> Engagement Ring</a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="1" value="Wedding Band" shortcode="WB"> <i class="icon-diamond fs-12"></i> Wedding Band</a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="45" value="Resize" shortcode="R"><i class="icon-diamond fs-12"></i> Resize</a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="60" value="Ring Collection" shortcode="RC"><i class="icon-diamond fs-12"></i> Ring Collection</a></li>';
                setDropdown += '<li><a href="javascript:;" getDuration="75" value="Ring Drop Off" shortcode="RDO"><i class="icon-diamond fs-12 "></i> Ring Drop Off</a></li>';

              setDropdown += '</ul></li>';
        setDropdown += '</ul>';
        return setDropdown;
  }

  function geDateValues(getSelectedDate)
  {
    
    var d = new Date(getSelectedDate);
    var getDate = d.getDate();
    if(getDate < 10)
      { getDate = '0'+getDate; }
    var getSuffixDate = getSuffix3(getDate);
    var getDateSuffix = getDate+getSuffixDate;

    var getMonth = moment(getSelectedDate).format('MMMM');
    var getMonthNumber = moment(getSelectedDate).format('MM');
    
    var monthShortName = moment.monthsShort(getMonthNumber - 1);

    var getCurrentYear= moment(getSelectedDate).weekday(a).format('YYYY');

    $('#bookingDate').attr('monthname',monthShortName);
    $('#bookingDate').attr('bookingyear',getCurrentYear);
    $('#bookingDate').attr('datenumber', getDate);
    var getDay = $('#bookingDate').attr('dayname');
    
    
    var getTime = $('#bookingDate').attr('timeslotfull');
    var getTimeFull = fullTimeFormat(getTime);
    var getRoom = $('.thisLabelClicked').attr('roomnumber');

    $('#bookingDate').attr('roomNumber', getRoom);
    var getComlpeteDate = getYear+'-'+getMonthNumber+'-'+getDate;
    $('#bookingDate').attr('ComlpeteDate', getComlpeteDate);

    
    var updatedTime = $('#bookingDate').attr('updatedTime');
    var setHtml = getDay + ' ' + getDateSuffix + ' ' + monthShortName + ', ' + getYear + ' ' + updatedTime;
    $('#bookingDate').html(setHtml); 
  }


/* ==================================================== */
// Cancel popup on scroll
$('section.rightCol').on('scroll', function(event){
      $('.addBookingPopup').html('');
      $('.addBookingPopup').addClass('hide');
      $('.rightCol').removeClass('overflowHidden');
      //$('section.rightCol').removeClass('hidden');
  });// End


// close dropdown on outside click 
    $(document).on('click', function(event){
        var container = $(".tempContainer, .addBookingLink");
        if (!container.is(event.target) &&            
            container.has(event.target).length === 0)
            {
              $('.rightCol').removeClass('overflowHidden');
              $('.addBookingPopup').html('');
              $('.addBookingPopup').addClass('hide');
            }
    });// End


  $(document).on('click', '.customerresult a', function () { 

      $('.saveBookingError').addClass('hide');
      var value = $(this).html();
      var getLeadId = $(this).attr('leadid');

      if(window.AppointmentType == 1)
        { AppointmentTypeLeadGet(getLeadId) }
      
      
      $(this).closest('.borderBottom').find('.customerName').html(value).attr('value',value);
      $(this).closest('.borderBottom').find('.subheading').removeClass('hide');
      $(this).closest('.borderBottom').find('.newbookingdropdown').addClass('hide');
      

      //$('#customerRepSelect').addClass('hide'); 
      
    });// End




  function AppointmentTypeLeadGet(getLeadId)
    {

        var getAssigneeId = window.selectedAssigneeId;
        var getWeeklyDate = $('.calendarWeeklyDate').attr('startdate');
        
        //loadQuestionViewcalnder(getAssigneeId, getWeeklyDate);

      $.ajax({
                type: "GET",
                url: "/dashboard/ajaxGetLeadDetailForLeadPage",
                data: {lead_id:getLeadId},
                success: function (data) {
                  var parsed = '';
                  try
                  {
                    parsed = JSON.parse(data);                  
                  }
                  catch(e)
                  {                  
                   return false;                    
                  }
                  var html = "";
                 
                  // Get User Color
                  var getThisUserId = parsed.assign_to_UserId;
                  $.ajax({

                    type: "GET",
                    url: "/dashboard/ajaxGetUserColor",
                    data: {user_id : getThisUserId},
                    success: function (data) {
                      var parsed = '';          
                      try{                           
                        parsed = JSON.parse(data);              
                      }                 
                      catch(e)                
                      {                  
                        return false;                  
                      }
                      
                      window.userColor = '#'+parsed["0"].color;
                      
                    }
                  });
                  
                  //Setting Lead Id
                  
                  $('.thisLeadId').attr('leadId',parsed[0].Lead_id);
                  // Product
                  $("#productDropdown").html(parsed[0].product_title);
                  $('#onlyReferral').val(parsed[0].LeadReferredCustomerName);
                  $(".referral.dropdown a.selected-text").attr("value",parsed[0].how_heard_title);
                  $('.additional-details .requirements').val(parsed[0].LeadLookingFor);
                  $('.additional-details .instructions').val(parsed[0].LeadSpecialInstructions);
                  $('.additional-details .ReferenceProduct').val(parsed[0].reference_product);
                  $('#budgetDropdown').closest('a.selected-text').attr('value',parsed[0].LeadBudget); 
                  
                  $('#referrenceDropdown').val(parsed[0].LeadReference);                 
                  $('.initialScreen').removeClass('hideshow'); 
                }
            }); 
    }




/* ==================================================== */


// Weekly Dates 

  //var date = '2018-06-12';
  var thisdate = new Date();
  loadWeeklyDates(thisdate);
  function loadWeeklyDates(date) { 
      
      // 25/06/2018 - 01/07/2018
      
      var getStartDateDisplay = moment(date).weekday(0).format('DD/MM/YYYY');
      var getNextWeekStartDateDisplay = moment(date).weekday(7).format('DD/MM/YYYY');      

      var getStartDate = moment(date).weekday(0).format('YYYY-MM-DD');
      var getNextWeekStartDate = moment(date).weekday(7).format('YYYY-MM-DD');
      var getPreviousWeekStartDate = moment(date).weekday(-7).format('YYYY-MM-DD');
      var getCurrentYear = moment(date).weekday(6).format('YYYY');
      var getMonthNumberCurrent = moment(date).format('M');
      var getMonthNumber = moment(getNextWeekStartDate).format('M');
      var currentDayDate = moment(thisdate).format('YYYY-MM-DD');
      setFirstDate = '';
      setLastDate = '';

      for (var a = 0; a < 7; a++) {

          var getCurrentDate = moment(date).weekday(a).format('D');
          //var getCurrentMonth = moment(date).weekday(a).format('MMMM');
          //var getCurrentMonth = moment(date).weekday(a).format('MMMM');
          
          var monthShortName = moment.monthsShort(getMonthNumber - 1);
          var monthShortNameEdit = moment.monthsShort(getMonthNumberCurrent - 1);
          
          var dateSuffix = ''
          if(getCurrentDate == 1 || getCurrentDate == 21 || getCurrentDate == 31)
          {
              dateSuffix = 'st'
          }
          else if(getCurrentDate == 2 || getCurrentDate == 22)
          {
              dateSuffix = 'nd'   
          }
          else if(getCurrentDate == 3 || getCurrentDate == 23)
          {
              dateSuffix = 'rd' 
          }
          else
          {
              dateSuffix = 'th'    
          }
          if(a == 0)
          {
              setFirstDate = getCurrentDate + dateSuffix + ' - '; 
          }
          else if(a == 6)
          {
              setLastDate = getCurrentDate + dateSuffix
          }

          //var completeDate = setFirstDate + setLastDate + ' '+ monthShortName + ', ' + getCurrentYear;
          var completeDate = getStartDateDisplay + ' - '+ getNextWeekStartDateDisplay;

          $('#bookingDate').attr('monthName', monthShortName);
          $('#bookingDate').attr('monthNameEdit', monthShortNameEdit);
          $('#bookingDate').attr('bookingYear', getCurrentYear);

      }
      $('#bookingDate').attr('monthNumber', getMonthNumberCurrent);
      $('.weeklyDates span').html(completeDate).attr('startDate',getStartDate);
      $('.weeklyDates span').attr('nextWeekDate',getNextWeekStartDate);
      $('.weeklyDates span').attr('previousWeekDate',getPreviousWeekStartDate);
      $('.weeklyDates span').attr('currentDayDate',currentDayDate);
      
      // Hide past Dates
      var startDate = new Date();
      var endDate = new Date(getStartDate);
      if (startDate > endDate) {
        //$('.weeklyDates label.arrowPrev').addClass('hide');
      }
      else
      {
        //$('.weeklyDates label.arrowPrev').removeClass('hide');
      }

  }

/*====================================================*/

/*------------------New Calendar Js ---------------------*/
/*------------------New Calendar Js ---------------------*/
/*------------------New Calendar Js ---------------------*/

/*---------------------------------------------*/


/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/


// Starting Calendar 
var getDate = getMonthNumber+'/1/'+getYear;
setTimeout(function(){ 
    $('#multiCalendar').multiDatesPicker({
        altField: '#altField'
    });
}, 3000);


/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

    // Trigger Calendar

    $(document).on('click', '.triggerCalendar', function () {
        suggestedDate();
    });

    $(document).on('click', '.durationSelection a, .timeSelection a, .daySelection a, .weekSelection a', function () {
        if($('.durationSelection a').hasClass('active') && $('.timeSelection a').hasClass('active'))
            {
                suggestedDate();
            }
    });

    
    function suggestedDate() {

        var getAssigneeId = window.selectedAssigneeId;
        startCalendarLoading();
        var getWeeklyDate = $('.calendarWeeklyDate').attr('startdate');
        
        loadQuestionViewcalnder(getAssigneeId, getWeeklyDate);
    }

    suggestedDate();

/*--------------------------------------------------*/
/*--------------------------------------------------*/
/*--------------------------------------------------*/


  // Save and Book Information

  $(document).on('click', '.savenBookBtn', function (){
      window.saveAndBook = true;
      $('#submitbutton').trigger('click');
      if(window.validState == true)
      {
        $(".hideOnSavenBook").addClass('hide');
        $(".calendarShowOnBook").removeClass('hide');  
      }
      
  });// End

/*--------------------------------------------------*/
/*--------------------------------------------------*/
/*--------------------------------------------------*/
  //  Only Save Information
  $(document).on('click', '.onlySaveBtn', function (){
      window.saveAndBook = false;
      $('#submitbutton').trigger('click');
  });// End

/*--------------------------------------------------*/
/*--------------------------------------------------*/
/*--------------------------------------------------*/

  //  Canlcel Booking Popup

  $(document).on('click', '.onlyCancelBtn', function (){
      var checkIfFieldsHasSomeValue = false;
      
      
      $('.firstname, .lastname, .phonenumber, .checkEmailCount, #fullAddress, #onlyReferral, #specify_requirements, #ReferenceProduct, #referrenceDropdown, .instructions').each(function() {
        var checkValue = $(this).val();
        if (checkValue != '') {
          checkIfFieldsHasSomeValue = true;
        }
      });
      
      $('.formfields a.selected-text, .additional-details a.selected-text' ).each(function() {
        var checkValue = $(this).attr('value');
        if (checkValue != 'All') {
          if (checkValue != 'Reason') {
            checkIfFieldsHasSomeValue = true;
          }
        }
      });

      if(checkIfFieldsHasSomeValue)
      {
        $('.dialogeBox.calcelLeadInfo').removeClass('hide');
      }


  });// End

/*--------------------------------------------------*/
/*--------------------------------------------------*/
/*--------------------------------------------------*/

  //  Cancel Booking
  $(document).on('click', '.NoCancelBooking', function (){
      $('.dialogeBox.calcelLeadInfo').addClass('hide');
  });// End

/*--------------------------------------------------*/
/*--------------------------------------------------*/
/*--------------------------------------------------*/


  //  Cancel Booking
  $(document).on('click', '.yesCancelBooking', function (){

     $('.dialogeBox.calcelLeadInfo').addClass('hide');
     $('.basicInfo').html(window.getBasicInfo);
     $('.additional-details').html(window.getAdditionalInfo);
     setTimeout(function(){ 
        $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
     }, 3000);
     

  });// End

/*--------------------------------------------------*/
/*--------------------------------------------------*/
/*--------------------------------------------------*/

    // Validation


    function validation()
    {
        var ifemailPopulated = $("#email").attr('readonly');
        var ifphonePopulated = $("#phonenumber").attr('readonly');
        $('.firstError').addClass('opacity0');
        var title = $('.dropdown.title a.selected-text').attr('value');
        var gender = $('.dropdown.Gender a.selected-text').attr('value');
        var firstname = $('.firstname').val();
        var lastname = $('.lastname').val();
        var getPhone = $('#phonenumber').val();
        var getEmail = $('#email').val();
        var getState = $('#stateDropdown').closest('a.selected-text').attr('value');
        var getProduct = $('#productDropdown').closest('a.selected-text').attr('value');
        var getReferral = $('#referralDropdown').closest('a.selected-text').attr('value');
        var getBudget = $('#budgetDropdown').closest('a.selected-text').attr('value');
        var getAgent = $('#assign_us_Dropdown').closest('a.selected-text').attr('value');
        var getCountry = $('#countryName').attr('value');
        var getCity = $('#cityValue').val();
        var checkCountry = true;
        if(getCountry == 'Australia')
        {
            if(getState == 'All')
            {
              checkCountry = false
            }
            else
            {
              checkCountry = true
            }
        }
        
        if(title == 'All' || gender == 'All' || firstname == '' || lastname == '' || getPhone == '' || getEmail == '' || getProduct == 'All' || getBudget == 'All' || getAgent == 'All' || checkCountry == false)
        {
            if(title == 'All')
            {$('.titleError').removeClass('opacity0');}

            if(gender == 'All')
            {$('.genderError').removeClass('opacity0');}

            if(firstname == '')
            {$('.firstname').closest('.relative').find('.requiredError').removeClass('opacity0');}

            if(lastname == '')
            {$('.lastname').closest('.relative').find('.requiredError').removeClass('opacity0');}

            if(getPhone == '')
            {$('#phonenumber').closest('.relative').find('.requiredError').removeClass('opacity0');}

            if(getEmail == '')
            { 
              $('#email').closest('.relative').find('.requiredError').removeClass('opacity0');
            }
            else if(!isValidEmailAddress(getEmail))
            {
              $('.emailexists').addClass('opacity0');
              $('#email').next('label').next('label').removeClass('opacity0');
            }

            if(getProduct == 'All')
            { $('.producterror').removeClass('opacity0'); }
            

            if(getBudget == 'All')
            { $('.budgeterror').removeClass('opacity0'); }

            if(getAgent == 'All')
            { $('.agenterror').removeClass('opacity0'); }

            if(checkCountry == false)
            { 
                $('.stateerror').removeClass('opacity0'); 
            }
            
            $(".rightCol").animate({ scrollTop: 0 }, "slow");
            window.validState = false;
            return false;
        }
        else
          { 
              
              if(ifemailPopulated == 'readonly')
              {
                window.validState = true; 
              }
              else if(isValidEmailAddress(getEmail))
              { 
                var getLeadId =  $('.thisLeadId').attr('leadid');
                var popuplatedemail = $('#email').hasClass('popuplatedemail');
                if(popuplatedemail == true )
                {
                  if(window.emailexists == true)
                  {
                    
                    $(".rightCol").animate({ scrollTop: 0 }, "slow");
                    window.validState = false;
                    return false;
                  }
                  else
                  {
                    window.validState = true;  
                  }
                  
                }
                else
                {
                  if($('.emailexists').hasClass('green'))
                  {
                    window.validState = true;
                  }
                  else
                  {
                    if(window.AppointmentType == 1)
                    {
                      window.validState = true;
                    }
                    else
                    {
                      $(".rightCol").animate({ scrollTop: 0 }, "slow");
                      window.validState = false;
                      return false;
                    }
                    
                  }
                }
              }
              else
              {
                $('.emailexists').addClass('opacity0');
                $('#email').next('label').next('label').removeClass('opacity0');
                
                $(".rightCol").animate({ scrollTop: 0 }, "slow");
                window.validState = false;
                return false;
              }
              if(ifphonePopulated == 'readonly')
              {
                window.validState = true;
              }
              else if(validatePhone(getPhone))
              {
                if($('#phonenumber').attr('passon') == 'true')
                {
                  $('.phonefield .firstError,.phonefield .requiredError,.phonefield .phoneexists').addClass('opacity0');
                  window.validState = true;
                }
                else
                {
                  $('.phonefield .phoneexists').removeClass('opacity0');
                  $(".rightCol").animate({ scrollTop: 0 }, "slow");
                  window.validState = false;
                  return false;
                }
              }
              else
              {
                $('.phonefield .firstError').removeClass('opacity0');
                $(".rightCol").animate({ scrollTop: 0 }, "slow");
                window.validState = false;
                return false;
              }
          }

    }

    // Book Now Open
    $(document).on('click', '.btn-bookNow', function () {
      $('.next-saveDiv').addClass('hide');
      $('.NewCalendarContainer').removeClass('hide').addClass('openNow');
       suggestedDate();
        //validation();
    });
    

    /*-------------------------------------------------*/

    // Save Booking     
    $(document).on('click', '.btn-saveBooking', function () {
        
        var checkBookingDate = $('#bookingDate').hasClass('nowCanSave');
        var checkBookingValue = $('.suggestedDate').html();
        
        if(checkBookingValue == "")
        {
            showBookingError();
            return false;    
        }


        $('.next-saveDiv').removeClass('hide');
        $('.bookNowDiv').addClass('hide');
        additionalDetailsExpand();
        $('.btn-bookNow').addClass('hide');
        $('.savedBooking').removeClass('hide');
        $('.bookingViewIcon').addClass('hide');
        $('.bookingHeading').removeClass('hide');

    });  

/*------------------------------------------------*/

    // Book Now Calendar Close
    $(document).on('click', '.cancelBooking', function () {
        $('.next-saveDiv').removeClass('hide');
        $('.NewCalendarContainer').addClass('hide');
        additionalDetailsExpand();
        $('.savedBooking').addClass('hide');
        $('.btn-bookNow').removeClass('hide');

        $('#bookingDate').removeClass('nowCanSave');
        $('.suggestedDate').html('');
    });

/*------------------------------------------------*/

    // Book Now Calendar Close
    $(document).on('click', '.cancelBookedbooking', function () {
        
        $('.roomBooking.newlyAdded').remove();
        $('.savedBooking').addClass('hide');
    });

    /*------------------------------------------------*/

    // Book Now Calendar Close
    $(document).on('click', '.editBooking', function () {
        $('.next-saveDiv').addClass('hide');
        additionalDetailsMinimize();
        $('.savedBooking').removeClass('hide');
        $('.btn-bookNow').addClass('hide');
        $('.bookingViewIcon').removeClass('hide');
        $('.bookingHeading').addClass('hide');

        if($('.NewCalendarContainer').hasClass('openNow'))
        {
          $('.NewCalendarContainer').removeClass('hide').addClass('openNow');
          suggestedDate();
        }
        else
        {
          $('.NewCalendarContainer').removeClass('hide').addClass('openNow');
          suggestedDate();
        }
    });

/*--------------------------------------------------*/

    /*Cancel calendar view booking*/
    $(document).on('click','.btnCancelBooking', function() {
        $('.booking-date, .booking-hour, .booking-assigned').attr('value', '');
        $('.booking-date, .booking-hour, .booking-assigned').val('');
    });

/*--------------------------------------------------*/

    // Change to booking View

    $(document).on('click','.bookingViewIcon', function() {
        
        $('.bookNowDiv').slideUp(150);
        $('.calenderView').css("height","auto");

    });
    
    // Change to Question View

    $(document).on('click','.QuestionViewIcon', function() {
        $('.calenderView').css("height","0");
        $('.bookNowDiv').slideDown(300);
    });
    
    // Search Dropdown Start 

    // dropdown 
    $(document).on('click','.selected-text',function(){
        var getObject = $(this).closest('li').next('li').find('ul.dropdownOptions');
        
        if($(this).hasClass('LeadsCalendar'))
        {
          if(getObject.is(':hidden'))
          {
            $('ul.dropdownOptions').slideUp(0);
            getObject.fadeIn(150);
          }
          else
          {
            getObject.fadeOut(100);
          }
        }
        else
        {
          if(getObject.is(':hidden'))
          { 
            $('ul.dropdownOptions').slideUp(0);
            getObject.slideDown(250);
          }
          else
          {
            getObject.slideUp(150);
          }
        }

        $('.calendarfields').addClass('opacity0');
    });// End

    // -----------------------------------------------------

    // close dropdown on outside click 
    $(document).on('click', function(event){
        var container = $(".dropdown");
        if (!container.is(event.target) &&            
            container.has(event.target).length === 0)
            {
                if($('ul.dropdown').find('ul.dropdownOptions').is(':visible'))
                {$('ul.dropdown').find('ul.dropdownOptions').slideUp(150);}
            }
    });// End

    $("#specify_requirements").on('change', function(event){
        $(this).closest('.relative').find('span').show();
    });

    // Add other product

    $(document).on('keyup', '#otherProductInput', function () {
      var getValue = $(this).val();
      $(this).attr('data-input',getValue);
    });// End

    $(document).on('click','.addproduct', function() {
        
        var getProduct = $('.calendarLoad .addBookingPopup #otherProductInput').val();
        window.otherProduct = getProduct;
        var getDuration = $('.calendarLoad .addBookingPopup .appointmentTypesOther a.selected-text').attr('getduration');
        var getDuration2 = $('.calendarLoad .addBookingPopup .appointmentTypesOther a.selected-text').attr('getduration2');
        if(getProduct == '')
        {
          return false;
        }
        else
        {
          $('.calendarLoad .addBookingPopup .appointmentTypes ul.dropdownOptions li:first-child').trigger('click');
          $('.calendarLoad .addBookingPopup .appDetails .dropdown.appointmentType a.selected-text span').html(getProduct);
          $('.calendarLoad .addBookingPopup .appDetails .dropdown.appointmentType a.selected-text').attr('value', getProduct);
          $('.calendarLoad .addBookingPopup .appDetails .dropdown.appointmentType a.selected-text').attr('getduration', getDuration);
          $('.calendarLoad .addBookingPopup .newBookingDuration .subheading').attr('value', getDuration2);
          var setDurationHtml = '<span class="subheading" value="'+getDuration2+'">Duration</span> '+getDuration+' Hours';

          $('.calendarLoad .addBookingPopup .customerNameOther').removeClass('hide');
          $('.calendarLoad .addBookingPopup .customerNamedefault').addClass('hide');
          $('.calendarLoad .addBookingPopup .newBookingDuration').html(setDurationHtml);
            setTimeout(function(){ 
              $('.calendarLoad  .addBookingPopup .appDetails .dropdown.appointmentType ul.dropdownOptions').css('height', '226px');
              
              window.other =  true;
          }, 500);
        }
    });

    // Select dropdown value
    
    

    $(document).on('click','ul.dropdownOptions li:not(.assignToDiv ul.dropdownOptions li)',function(){
        window.other = false;
        var el = $(this); 
        
        var getValue = $(this).find('a').attr('value');
        var getAsignee_Id = $(this).find('a').attr('id');


        
        el.closest('.dropdown').find('a.selected-text span').html(getValue);
        el.closest('.dropdown').find('a.selected-text').attr('value', getValue);
        el.closest('.dropdown').find('ul.dropdownOptions').slideToggle(50);
        el.closest('.dropdown').prev('span.text-top').slideDown(50);
        el.closest('.dropdown').next('select').find('option').attr('value',getValue);
        el.closest('.dropdown').next('label').addClass('opacity0');



        //If Duation time new booking popup
        if(el.closest('.dropdown').hasClass('durationTime'))
        { 
          var checkTime = $(this).find('a').attr('getDuration');
          var checkTime2 = $(this).find('a').attr('getDuration2');
          el.closest('.dropdown').find('a.selected-text').attr('getDuration', checkTime); 
          el.closest('.dropdown').find('a.selected-text').attr('getDuration2', checkTime2); 
        }

        if(el.closest('.dropdown').hasClass('appointmentType'))
        { 
          
          
          if(getValue == "other")
          {

            $('.addBookingPopup .appointmentTypesOther').removeClass('hide');
            $('.addBookingPopup .newBookingDetail').addClass('hide');
            $('.addBookingPopup .customerNameOther').removeClass('hide');
            $('.addBookingPopup .customerNamedefault').addClass('hide');
            
            window.other = true;
            return false;
          }
          else
            {
              window.other = false;
              var getIcon = $(this).find('a i').attr('icon');
              window.productIcon = getIcon;
              window.mainProduct = getValue;
              $('.calendarLoad .addBookingPopup .productShortCode i').removeClass('icon-diamond').addClass(getIcon);
              $('.addBookingPopup .appointmentTypesOther').addClass('hide');
              $('.addBookingPopup .newBookingDetail').removeClass('hide');
              $('.addBookingPopup .customerNamedefault').removeClass('hide');
              $('.addBookingPopup .customerNameOther').addClass('hide');
            }

          var getShortCode = $(this).find('a').attr('shortcode');
          var getappDuaration = $(this).find('a').attr('getduration');
          var setMessage = 'Meeting room not available. Please select some other room!';

          if(getappDuaration == '1.5')
          { 
            if(window.getTimeAllowed < 6)
            {
              showBookingError(setMessage);
            }  
          }
          else if (getappDuaration == '1')
          { 
            if(window.getTimeAllowed < 4)
            {
              showBookingError(setMessage);

            } 
          }
          else
          { 
            if(window.getTimeAllowed < 2)
            {
              showBookingError(setMessage);

            } 
          }
          
          el.closest('.appointmentTypes').addClass('hide'); 
          el.closest('.appointmentTypes').next().next('.newBookingDetail').removeClass('hide'); 
          $('.addBookingPopup .newApp').addClass('hide'); 
          $('.addBookingPopup .appDetails .ellipsis').html(getValue);
          
           $('.addBookingPopup .productShortCode span').html(getShortCode);
          var getCurentDropdown = el.closest('.appointmentTypes').html();
          //el.closest('.appointmentTypes').remove();
          $('.addBookingPopup .appDetails').html(getCurentDropdown);
          $('.addBookingPopup .appDetails').removeClass('hide'); 
          $('.headBar .dropdownOptions').hide();
          var setPopupHtml = '';
          if(getappDuaration == '1.5')
          {  setPopupHtml += '<span class="subheading" value="90">Duration</span> 1.5 Hours'; }
          else if (getappDuaration == '1')
          { setPopupHtml += '<span class="subheading" value="60">Duration</span> 1 Hours'; }
          else
          { setPopupHtml += '<span class="subheading" value="30">Duration</span> 0.5 Hours'; }
          $('.newBookingDuration')

          $('.newBookingDuration').html(setPopupHtml);
        } 

        //If Preferred method
        if(el.closest('.dropdown').hasClass('preferredMethod'))
        {   
          if(getValue == "Other")
          {
            $('#perferrefDropdownOther').closest('.relative').removeClass('hide');
          }
          else
          {
            $('#perferrefDropdownOther').closest('.relative').addClass('hide');
          }
        }


        //If Preferred method
        if(el.closest('.dropdown').hasClass('referral'))
        {   
          if(getValue == "Other")
          {
            //$('#referralDropdownOther').closest('.relative').removeClass('hide');
          }
          else
          {
            //$('#referralDropdownOther').closest('.relative').addClass('hide');
          }
        }


        //If Title
        if(el.closest('.dropdown').hasClass('title'))
        {   
          
          if(getValue == "Mr")
          {
            $('.dropdown.Gender').find('a.selected-text').attr('value','Male');
            $('.dropdown.Gender').find('span').html('Male');
          }
          
          else if(getValue == "Unknown")
          {
            $('.dropdown.Gender').find('a.selected-text').attr('value','Other/Not Specified');
            $('.dropdown.Gender').find('span').html('Other/Not Specified');
          }
          else
          {
            $('.dropdown.Gender').find('a.selected-text').attr('value','Female');
            $('.dropdown.Gender').find('span').html('Female');
          }
          $('.dropdown.Gender').prev('.text-top').show();
          $('.Gender').next('.genderError').addClass('opacity0');
            //return false; 
        }

        //If Gender
        if(el.closest('.dropdown').hasClass('Gender'))
        {   
          
          if(getValue == "Male")
          {
            $('.dropdown.title').find('a.selected-text').attr('value','Mr');
            $('.dropdown.title').find('span').html('Mr');
          }
          
          else if(getValue == "Other/Not Specified")
          {
            $('.dropdown.title').find('a.selected-text').attr('value','Unknown');
            $('.dropdown.title').find('span').html('Unknown');
          }
          else
          {
            $('.dropdown.title').find('a.selected-text').attr('value','Mrs');
            $('.dropdown.title').find('span').html('Mrs');
          }
          $('.dropdown.title').prev('.text-top').show();
          $('.title').next('.titleError').addClass('opacity0');
            //return false; 
        }


        if(el.closest('.dropdown').hasClass('partnertitle'))
        {   
          
          if(getValue == "Mr")
          {
            $('.dropdown.partnerGender').find('a.selected-text').attr('value','Male');
            $('.dropdown.partnerGender').find('span').html('Male');
          }
          
          else if(getValue == "Unknown")
          {
            $('.dropdown.partnerGender').find('a.selected-text').attr('value','Other/Not Specified');
            $('.dropdown.partnerGender').find('span').html('Other/Not Specified');
          }
          else
          {
            $('.dropdown.partnerGender').find('a.selected-text').attr('value','Female');
            $('.dropdown.partnerGender').find('span').html('Female');
          }
          $('.dropdown.partnerGender').prev('.text-top').show();
          $('.partnerGender').next('.partnerError').addClass('opacity0');
            //return false; 
        }

        //If Gender
        if(el.closest('.dropdown').hasClass('partnerGender'))
        {   
          
          if(getValue == "Male")
          {
            $('.dropdown.partnertitle').find('a.selected-text').attr('value','Mr');
            $('.dropdown.partnertitle').find('span').html('Mr');
          }
          
          else if(getValue == "Other/Not Specified")
          {
            $('.dropdown.partnertitle').find('a.selected-text').attr('value','Unknown');
            $('.dropdown.partnertitle').find('span').html('Unknown');
          }
          else
          {
            $('.dropdown.partnertitle').find('a.selected-text').attr('value','Mrs');
            $('.dropdown.partnertitle').find('span').html('Mrs');
          }
          $('.dropdown.partnertitle').prev('.text-top').show();
          $('.partnertitle').next('.partnerError').addClass('opacity0');
            //return false; 
        }
        // If Calendar
        if(el.closest('.dropdown').hasClass('calendar'))
        {   
            return false; 
        }
        // If Lead status
        if(el.closest('.dropdown').hasClass('statusOfAgents'))
        {
            el.closest('.dropdown').find('a.selected-text i').removeClass('icon-downarrow').addClass('icon-close');
            loadLeads(); 
        }
        // If Lead referral
        if(el.closest('.dropdown').hasClass('agentReferral'))
        {
            el.closest('.dropdown').find('a.selected-text i').removeClass('icon-downarrow').addClass('icon-close');
            loadLeads(); 
        }

        //Check if budget dropdown
        if(el.closest('.dropdown').hasClass('budget'))
        {
            //GetUserBasedOnBudget(getValue);
            GetNextInLine(getValue);
            if($('.dropdownheightSet').hasClass('hide')) 
            { 
                $('.dropdownheightSet').hide().removeClass('hide'); 
            }
            $('.budgeterror').addClass('opacity0');

        }
        
        // Check if it is Title
        if(el.closest('.dropdown').hasClass('title'))
        {
          //validateBasicInfo();
        }
        if(el.closest('.dropdown').hasClass('country'))
        {
          $('.stateerror').addClass('opacity0'); 
        }
        // Check if it is gender
        if(el.closest('.dropdown').hasClass('Gender'))
        {
          //validateBasicInfo();
        }
        // Check if State dropdown
        if(el.closest('.dropdown').hasClass('State'))
        {   
          var getStateId = $(this).find('a').attr('stateid');   
          el.closest('.dropdown').find('a.selected-text').attr('stateId',getStateId );
        }

        // Check if City dropdown
        if(el.closest('.dropdown').hasClass('City'))
        {   
            $('.cityerror').addClass('opacity0');
        }

        // Check if Product dropdown
        if(el.closest('.dropdown').hasClass('product'))
        {   
          var getProductId = $(this).find('a').attr('productId');   
          el.closest('.dropdown').find('a.selected-text').attr('productId',getProductId );
          $('.producterror').addClass('opacity0');
          var getShortCode = $(this).find('a').attr('shortcode');
          el.closest('.dropdown').find('a.selected-text').attr('shortcode', getShortCode);
        }

        // Check if Referral dropdown
        if(el.closest('.dropdown').hasClass('referral'))
        {   
          var gethowHeardtId = $(this).find('a').attr('howHeardId');   
          el.closest('.dropdown').find('a.selected-text').attr('howHeardId',gethowHeardtId );
          $('.referralerror').addClass('opacity0');
        }

        // 
        // Check if meeting Rooms
        if(el.closest('.dropdown').hasClass('meetingRooms'))
        {   
            $('.meetingRoomValue').attr('value',getValue);
        }
        // Check if Leave use dropdown
        if(el.closest('.dropdown').hasClass('assignToDivLeave'))
        {   
            
            $('.assignToDivLeave a.selected-text').attr('asigneeId-value',getAsignee_Id);

            var startDate = $("#dateRange").attr('startDate');
            var endDate = $("#dateRange").attr('endDate');
            if(startDate == endDate)
            {
              endDate = '';
            }
            var Id = $(".assignToDivLeave a.selected-text").attr('asigneeId-value');
            ifUserOnLeave(startDate,endDate,Id);
        }
            
    });// End
    

    function showBookingError(setMessage)
    {
     // $('.showMessage div').html(setMessage);
      //$('.showMessage').addClass('topShow');
      //  setTimeout(function(){ 
      //    $('.showMessage').removeClass('topShow');
      //}, 5000); 
      $('.dialogeBox.leaveCheck .boxmessage').html(setMessage);
      $('.dialogeBox.leaveCheck').removeClass('hide');
          setTimeout(function(){ 
            $('.dialogeBox.leaveCheck').addClass('hide');
      }, 2000);

      $('.addBookingPopup').html('');
      $('.addBookingPopup').addClass('hide');
      return false;
    }

    // Check if use is on Leave
    window.datechange = 0;
    $(document).on('change','#dateRange' ,function(){
        
        window.datechange++;
        if(window.datechange > 1)
        {
            var startDate = $("#dateRange").attr('startDate');
            var endDate = $("#dateRange").attr('endDate');
            if(startDate == endDate)
            {
              endDate = '';
            }
            var Id = $(".assignToDivLeave a.selected-text").attr('asigneeId-value');
            ifUserOnLeave(startDate,endDate,Id); 
        }

    });// End

    // Select dropdown for Assign To Div
    
    $(document).on('click','.assignToDiv ul.dropdownOptions li',function(){

            var el = $(this);

            $('ul.dropdownOptions li').removeClass('activeField');

            var getShortCode = $(this).find('a').attr('shortcode'); 
            el.closest('.dropdown').find('a.selected-text').attr('shortcode', getShortCode);

            el.addClass('activeField');

            if($(this).hasClass('nextInline'))
            {
                var chek = "";
            }
            if(el.hasClass('nextInline'))
            { 
                var getId = $(this).find('a').attr('id');
                // Get User Color
                $.ajax({

                  type: "GET",
                  url: "/dashboard/ajaxGetUserColor",
                  data: {user_id : getId},
                  success: function (data) {
                    var parsed = '';          
                    try{                           
                      parsed = JSON.parse(data);              
                    }                 
                    catch(e)                
                    {                  
                      return false;                  
                    }
                    window.userColor = '#'+parsed["0"].color;
                    
                  }
                });

                window.selectedAssigneeId = getId;
                var getValue = $(this).find('a').attr('value');
                el.closest('.dropdown').find('a.selected-text span').html(getValue);
                el.closest('.dropdown').find('a.selected-text').attr('value', getValue);
                el.closest('.dropdown').find('a.selected-text').attr('assigneId', getId);
                el.closest('.dropdown').find('a.selected-text').attr('data-src', $(this).find('a img').attr('src'));
                el.closest('.dropdown').find('ul.dropdownOptions').slideToggle(150);
                el.closest('.dropdown').prev('span.text-top').slideDown(150);
                el.closest('.dropdown').next('select').find('option').attr('value',getValue);
                $('.otherReasonDiv').addClass('hide');
                // Basic Info Image set
                basicInfoUserDp();
                $('.btn-bookNow').addClass('canOpen');

                // Check if Agent selected
                var userId = $(this).find('a').attr('id');

                $('.agenterror').addClass('opacity0');

                if($('.NewCalendarContainer').hasClass('openNow'))
                {
                  suggestedDate();
                }
                

            }
            else
            {
                el.closest('.dropdownOptions').find('li').addClass('hide');
                $('.assignToDiv .otherSelection').slideDown(200);
                $('ul.assignToDiv .dropdownOptions').removeClass('dropdownheightSet');
            }
        });// End


    // Additional Agent dropdown Selection 


    $(document).on('click','.countryDiv .icon-dropdown' ,function(e){
        //$('.formfields span.ui-combobox button').trigger('click');
        e.preventDefault();
        return false;
    });// End

    // Additional Agent dropdown Selection 


    $(document).on('click','.Additionaldrodown p' ,function(){
         $('.selectReason, .selectReason2').addClass('opacity0');

        var el = $(this);
        $('.Additionaldrodown p').removeClass('activeReason');
        el.addClass('activeReason');
        var getValue = el.attr('value');
        var setValue = '<a href="javascript:;" value="'+getValue+'">'+getValue+'<span></span></a>';
        $('.Additionaldrodown div.additioanlSelection').html(setValue);
        $('.AdditionaldrodownList').slideUp(200);
        if(getValue == 'Other')
        {
            $('.other-explain').slideDown(300);
        }
    });// End

    // Agent selection skip

    $(document).on('click','.btn-skip',function(){

        
        var el = $(this);
        var checkDefaultValue = $('.additioanlSelection a').attr('value');
        var checkSelectedValue = $('.AdditionaldrodownList p').filter('.activeReason').attr('value');
        var inputValue = $('.other-explain input').val();
        if( checkDefaultValue == "Reasons" )
        {
          $('.selectReason').removeClass('opacity0');
          return false;
        }
        if( inputValue == "" && checkDefaultValue == "Other")
        {
          $('.selectReason2').removeClass('opacity0');
          return false;
        }
        else
        {
          $('.selectReason2').addClass('opacity0');
        }
        
        
        $('.assignToDiv .otherSelection').hide();
        el.closest('.dropdownOptions').find('li').removeClass('hide');
        $('ul.assignToDiv .dropdownOptions').hide();
        
        $('.btn-bookNow').addClass('canOpen');
        var getAgent = $('ul.assignToDiv .dropdownOptions li').filter('.activeField').find('a').attr('value');
        var userId = $('ul.assignToDiv .dropdownOptions li').filter('.activeField').find('a').attr('id');

        // Get User Color

        $.ajax({

          type: "GET",
          url: "/dashboard/ajaxGetUserColor",
          data: {user_id : userId},
          success: function (data) {
            var parsed = '';          
            try{                           
              parsed = JSON.parse(data);              
            }                 
            catch(e)                
            {                  
              return false;                  
            }
            window.userColor = '#'+parsed["0"].color;
            
          }
        });


        window.selectedAssigneeId = userId;
        el.closest('.dropdown').find('a.selected-text span').html(getAgent);
        el.closest('.dropdown').find('a.selected-text').attr('value', getAgent);
        el.closest('.dropdown').find('a.selected-text').attr('assigneId', userId);
        $('.otherReasonDiv').addClass('hide');
        $('.dropdown.assignToDiv').prev('span.text-top').slideDown(150);
        // Basic Info Image set
        basicInfoUserDp();
        $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
        // check dropdown selected value

        

        var check = $('.Additionaldrodown').hasClass('novalue');


        if( checkDefaultValue == "Reasons" )
        {
            $('.dropdownOptions').slideUp(150);

            $('.agenterror').addClass('opacity0');
            if($('.daySelection').hasClass('setForCalendar'))
            {
              
              suggestedDate()
            }
        }
        else
        {
            if(checkSelectedValue == 'Other')
            { 
                var el = $(this);
                var SetValue = "Other: " + $('.other-explain input').val();
                setTimeout(function(){ 

                    $('.dropdownOptions').slideUp(150);
                    $('.other-explain').closest('.dropdownOptions').find('li').removeClass('hide');
                    $('.other-explain').closest('.dropdown').find('a.selected-text span').html(getAgent);
                    $('.other-explain').closest('.dropdown').find('a.selected-text').attr('value', getAgent);
                    $('.other-explain').hide();
                    // Other reason dropdown setting
                    $('.otherReasonDiv .dropdown').find('a.selected-text span').html(SetValue);
                    $('.otherReasonDiv .dropdown').find('a.selected-text').attr('value', SetValue);
                    $('.otherReasonDiv').removeClass('hide');
                    $('.otherReasonDiv span.text-top').slideDown(150);
                    
                    $('.otherReasonExplained a').attr('value', SetValue);
                    $('.otherReasonExplained a').html(SetValue);
                    basicInfoUserDp();
                    $('.other-explain input').val('');
                    $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
                    // Reset Additional Dropdown
                    var setValue2 = '<a href="javascript:;" value="Reasons">Reasons<span></span></a>';
                    $('.Additionaldrodown div.additioanlSelection').html(setValue2);
                    $('.agenterror').addClass('opacity0');
                    // Checking if calendar is initialized
                    if($('.NewCalendarContainer').hasClass('openNow'))
                    {
                      suggestedDate();
                    }

                }, 500);
            }
            else
            {
                setTimeout(function(){ 
                    $('.dropdownOptions').slideUp(150);
                    el.closest('.dropdownOptions').find('li').removeClass('hide');
                    el.closest('.dropdown').find('a.selected-text span').html(getAgent);
                    el.closest('.dropdown').find('a.selected-text').attr('value', getAgent);
                    $('.other-explain').hide();
                    // Other reason dropdown setting
                    $('.otherReasonDiv .dropdown').find('a.selected-text span').html(checkSelectedValue);
                    $('.otherReasonDiv .dropdown').find('a.selected-text').attr('value', checkSelectedValue);
                    $('.otherReasonDiv').removeClass('hide');
                    $('.otherReasonDiv span.text-top').slideDown(150);
                    $('.assignToDiv').closest('.relative').find('.text-top').slideDown(150);
                    $('.otherReasonExplained a').attr('value', 'Other');
                    $('.otherReasonExplained a').html('Other');
                    // Basic Info Image set
                    basicInfoUserDp();
                    $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
                    var setValue2 = '<a href="javascript:;" value="Reasons">Reasons<span></span></a>';
                    $('.Additionaldrodown div.additioanlSelection').html(setValue2);
                    $('.agenterror').addClass('opacity0');
                    // Checking if calendar is initialized
                    if($('.NewCalendarContainer').hasClass('openNow'))
                    {
                      suggestedDate();
                    }

                }, 500);
            }
        }
        
        

    });// End

    $(document).on('click','.btn-skip2',function(){

        
        var el = $(this);
        var checkDefaultValue = $('.additioanlSelection a').attr('value');
        var checkSelectedValue = $('.AdditionaldrodownList p').filter('.activeReason').attr('value');
        var inputValue = $('.other-explain input').val();        
        
        $('.assignToDiv .otherSelection').hide();
        el.closest('.dropdownOptions').find('li').removeClass('hide');
        
        $('.btn-bookNow').addClass('canOpen');
        var getAgent = $('ul.assignToDiv .dropdownOptions li').filter('.activeField').find('a').attr('value');
        var userId = $('ul.assignToDiv .dropdownOptions li').filter('.activeField').find('a').attr('id');
        window.selectedAssigneeId = userId;
        el.closest('.dropdown').find('a.selected-text span').html(getAgent);
        el.closest('.dropdown').find('a.selected-text').attr('value', getAgent);
        el.closest('.dropdown').find('a.selected-text').attr('assigneId', userId);
        $('.otherReasonDiv').addClass('hide');
        // Basic Info Image set
        basicInfoUserDp();
        $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
        // check dropdown selected value

        

        var check = $('.Additionaldrodown').hasClass('novalue');


        if( checkDefaultValue == "Reasons" )
        {
            $('.dropdownOptions').slideUp(150);

            $('.agenterror').addClass('opacity0');
            if($('.daySelection').hasClass('setForCalendar'))
            {
              
              suggestedDate()
            }
        }
        else
        {
            if(checkSelectedValue == 'Other')
            { 
                var el = $(this);
                var SetValue = "Other: " + $('.other-explain input').val();
                setTimeout(function(){ 

                    $('.dropdownOptions').slideUp(150);
                    $('.other-explain').closest('.dropdownOptions').find('li').removeClass('hide');
                    $('.other-explain').closest('.dropdown').find('a.selected-text span').html(getAgent);
                    $('.other-explain').closest('.dropdown').find('a.selected-text').attr('value', getAgent);
                    $('.other-explain').hide();
                    // Other reason dropdown setting
                    $('.otherReasonDiv .dropdown').find('a.selected-text span').html(SetValue);
                    $('.otherReasonDiv .dropdown').find('a.selected-text').attr('value', SetValue);
                    $('.otherReasonDiv').removeClass('hide');
                    $('.otherReasonDiv span.text-top').slideDown(150);
                    $('.otherReasonExplained a').attr('value', SetValue);
                    $('.otherReasonExplained a').html(SetValue);
                    basicInfoUserDp();
                    $('.other-explain input').val('');
                    $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
                    // Reset Additional Dropdown
                    var setValue2 = '<a href="javascript:;" value="Reasons">Reasons<span></span></a>';
                    $('.Additionaldrodown div.additioanlSelection').html(setValue2);
                    $('.agenterror').addClass('opacity0');
                    // Checking if calendar is initialized
                    if($('.daySelection').hasClass('setForCalendar'))
                    {
                      suggestedDate()
                    }

                }, 500);
            }
            else
            {
                setTimeout(function(){ 
                    $('.dropdownOptions').slideUp(150);
                    el.closest('.dropdownOptions').find('li').removeClass('hide');
                    el.closest('.dropdown').find('a.selected-text span').html(getAgent);
                    el.closest('.dropdown').find('a.selected-text').attr('value', getAgent);
                    $('.other-explain').hide();
                    // Other reason dropdown setting
                    $('.otherReasonDiv .dropdown').find('a.selected-text span').html(checkSelectedValue);
                    $('.otherReasonDiv .dropdown').find('a.selected-text').attr('value', checkSelectedValue);
                    $('.otherReasonDiv').removeClass('hide');
                    $('.otherReasonDiv span.text-top').slideDown(150);
                    $('.otherReasonExplained a').attr('value', 'Other');
                    $('.otherReasonExplained a').html('Other');
                    // Basic Info Image set
                    basicInfoUserDp();
                    $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
                    var setValue2 = '<a href="javascript:;" value="Reasons">Reasons<span></span></a>';
                    $('.Additionaldrodown div.additioanlSelection').html(setValue2);
                    $('.agenterror').addClass('opacity0');
                    // Checking if calendar is initialized
                    if($('.daySelection').hasClass('setForCalendar'))
                    {
                      suggestedDate()
                    }

                }, 500);
            }
        }
        
        

    });// End


    // Agent selection Cancel
    $(document).on('click','.btn-cancelAgent',function(){
            
        var el = $(this);
        $('ul.dropdownOptions li').removeClass('activeField');
        $('.assignToDiv .otherSelection').hide();
        el.closest('.dropdownOptions').find('li').removeClass('hide');
        $('ul.assignToDiv .dropdownOptions').addClass('dropdownheightSet');
        $('.selectReason, .selectReason2').addClass('opacity0');
    });// End

    // Additional Agent other selection
    
    $(document).on('click','.Additionaldrodown  div.additioanlSelection',function(){
        var el = $(this);
        el.next('.AdditionaldrodownList').slideToggle(300);
    });// End


    function ifUserOnLeave(startDate,endDate,Id)
    {
      if(Id == null)
        {return false}
      var getUser = $('.assignToDivLeave a.selected-text').attr('value');
      if(endDate == '')
      {
        var leaveData =  {start_date : startDate , assign_UserId : Id}
      }
      else
      {
        var leaveData =  {start_date : startDate ,end_date : endDate , assign_UserId : Id}
      }
      $.ajax({
        type: "GET",
        url: "/dashboard/ajaxCheckUserIsOnLeave",
        data: leaveData, 
        success: function (data) {
          
          var parsed = '';          
          try{                           
            parsed = JSON.parse(data);              
          }                 
          catch(e)                
          {                  
            return false;                  
          }
          var count = 0;    
          if (parsed != null)
          {
            $.each(parsed, function(key, value){
              count++
            });
          }
        
          count;
          if(count > 0)
          {
            if(count == 1)
            {
              var setMessage = getUser + ' is on leave!';
            }
            else
            {
              var setMessage = getUser + ' is on leave in these days!';
            }

            //$('.showMessage').addClass('topShow');
            //  setTimeout(function(){ 
            //    $('.showMessage').removeClass('topShow');
            //}, 5000); 

            $('.dialogeBox.leaveCheck .boxmessage').html(setMessage);
            $('.dialogeBox.leaveCheck').removeClass('hide');
                setTimeout(function(){ 
                  $('.dialogeBox.leaveCheck').addClass('hide');
            }, 3000); 

            $('.btn-saveDetailsLeave').addClass('hide');
          }
          else
          {
            $('.btn-saveDetailsLeave').removeClass('hide');
          }
          
        }
      }); 
    }

    // Submit Form

    function getCustomerValues()
    {
        var preferredMethod = $('.dropdown.preferredMethod').find('.selected-text').attr('value');
        var preferredMethodOther = $("#perferrefDropdownOther").val();
        var preferredMethodVal = '';
        if ( preferredMethod == "Other"){ preferredMethodVal = preferredMethodOther; }
        else if(preferredMethod = 'All'){ preferredMethodVal = '' }
        else { preferredMethodVal = preferredMethod; }

        return {
            id : 0,            
            title : $('.title a.selected-text').attr('value'),
            gender : $('.Gender a.selected-text').attr('value'),  
            first_name : $("#first_name").val(),
            last_name : $("#last_name").val(),
            email : $("#email").val(),
            mobile : $("#phonenumber").val(),
            address1 : $("#fullAddress").val(),
            state_id : $(".dropdown.State").find('a.selected-text').attr('stateid'),
            country_id : $('#countryName').attr('value'),
            source : $("#CommunicationMethod").text(),
            contact_method : preferredMethodVal
        };
        
    }
    function getLeadValues()
    {
        var preferredMethod = $('.dropdown.preferredMethod').find('.selected-text').attr('value');
        var preferredMethodOther = $("#perferrefDropdownOther").val();
        var preferredMethodVal = '';
        if ( preferredMethod == "Other"){ preferredMethodVal = preferredMethodOther; }
        else if(preferredMethod == 'All'){ preferredMethodVal = '' }
        else { preferredMethodVal = preferredMethod; }

        var referralMethod = $("#referralDropdown").text();
        var referralMethodOther = $("#referralDropdownOther").val();
        var referralMethodVal = '';
        if ( referralMethod == "Other"){ referralMethodVal = referralMethodOther; }
        else { referralMethodVal = referralMethod; }

        return {
            customer_id: 0,
            lead_id : $('.thisLeadId').attr('leadId'),
            title : $('.title a.selected-text').attr('value'),
            gender : $('.Gender a.selected-text').attr('value'),  
            first_name : $("#first_name").val(),
            last_name : $("#last_name").val(),
            email : $("#email").val(),
            mobile : $("#phonenumber").val(),
            product : $(".dropdown.product").find('a.selected-text').attr('productId'), 
            budget : $('#budgetDropdown').closest('a.selected-text').attr('value'),
            lead_source : $("#CommunicationMethod").text(),
            how_heard : $(".dropdown.referral").find('a.selected-text').attr('howheardid'),   
            state_id : $(".dropdown.State").find('a.selected-text').attr('stateid'),
            lead_owner : $(".assignToDiv a.selected-text").attr("assigneid"),
            looking_for : $("#specify_requirements").val(),
            reference_product : $("#referrenceDropdown").val(),
            preferred_contact : preferredMethodVal,
            referred_by_customer : $("#onlyReferral").attr('referencecustomerid'),
            special_instructions : $("#specialinstructions").val()
        };

    }
    function getAppointmentValues()
    {
        var referralMethod = $("#referralDropdown").text();
        var referralMethodOther = $("#referralDropdownOther").val();
        var referralMethodVal = '';
        if ( referralMethod == "Other"){ referralMethodVal = referralMethodOther; }
        else { referralMethodVal = referralMethod; }
        return {
            product : $("#productDropdown").text(),            
            referral : $(".referral.dropdown a.selected-text").attr("value"), 
            only_referral : $("#onlyReferral").val(),
            specify_requirements : $("#specify_requirements").val(), 
            special_instructions : $("[name= 'special_instructions']").val(),
            budget : $('#budgetDropdown').closest('a.selected-text').attr('value'),           
            reson_skip_next_in_line : $("#skip_reason_dropdown").text(),
            reference_product : $("#referrenceDropdown").val(),
            assign_to : $("#assign_us_Dropdown").text(),
            assign_id : $(".assignToDiv a.selected-text").attr("assigneid"),                 
            booking_date : $("#bookingDate").attr("ComlpeteDate"),
            booking_time : $("#bookingDate").attr("timeslot"),
            booking_room : $('#bookingDate').attr('roomnumber'),
            durationTime : $('#bookingDate').attr('durationTime'),
            bookingstart : $('#bookingDate').attr('bookingstart'),
            customerName : $('#bookingDate').attr('customerName'),
            salesRepName : $('#bookingDate').attr('salesRepName'),
            color : window.userColor,
            AppointmentType : 0,
            isBooked : 0,
            appointment_id: $('#appointmentId').attr('appointmentId'),
            lead_id : $('.thisLeadId').attr('leadId')
        };
        
    }
    function getValuesOtherAppointment()
    {
        return {
            product : $("#productDropdown").text(),            
            budget : $('#budgetDropdown').closest('a.selected-text').attr('value'),
            assign_to : $("#assign_us_Dropdown").text(),
            assign_id : $(".assignToDiv a.selected-text").attr("assigneid"),                 
            booking_date : $("#bookingDate").attr("ComlpeteDate"),
            booking_time : $("#bookingDate").attr("timeslot"),
            booking_room : $('#bookingDate').attr('roomnumber'),
            durationTime : $('#bookingDate').attr('durationTime'),
            bookingstart : $('#bookingDate').attr('bookingstart'),
            fullName : $('#bookingDate').attr('customerName'),
            salesRepName : $('#bookingDate').attr('salesRepName'),
            color : '#9b9b9b',
            isBooked : 0,
            appointment_id: 0,
            lead_id : 90000
        };
        
    }



  
    // Save New Booking
    $(document).on('click','.saveNewBooking', function (e) {
      var check = window.other;
      window.saveAndBook = false;
      if(window.other == true)
      {  
        var otherappointment = getValuesOtherAppointment(); 
        $.ajax({
          type: "POST",
          url: "/dashboard/ajaxSaveAppointment",
          data: otherappointment, 
          success: function (data) {
            var checkData = data;
            showMainLoading();
            var getAssigneeId = window.selectedAssigneeId;
            var getWeeklyDate = $('.calendarLoad .calendarWeeklyDate').attr('startdate');
            loadQuestionViewcalnder(getAssigneeId, getWeeklyDate);
          }
        });  
      }
      else
      {   
        $('#submitbutton').trigger('click');
      }
      setTimeout(function(){ 
        $('.basicInfo').html(window.getBasicInfo);
        $('.additional-details').html(window.getAdditionalInfo);
      }, 1000); 

    });

    // Cancel New Booking
    $(document).on('click','.cancelNewBooking', function (e) {
      $('.rings a').removeClass('active');
      $('.rings a:last-child').addClass('active');
      loadLeads();
      //Setting header changes

      showMainLoading();
      $('.newLeaveContainer').hide();
      $('.newLead').addClass('maxHeightHide');
      $('.dashboardContainer').addClass('hide');
      $('.leavesContainer').addClass('hide');
      $('.leadsContainer').removeClass('hide');
      $('.new-Lead').removeClass('active');
      $('.dashboard-header').removeClass('hide');

      //Reset New lead form
      $('.newLead').html(window.getNewLeadAll);
      $('.newLead').removeClass('inEditMode');

    });
    
    /* ==================================================== */
    /* ==================== Save Form ===================== */
    /* ==================================================== */

    $(document).on('click','#submitbutton', function (e) {
        
        var checkBookingDate = $('#bookingDate').hasClass('nowCanSave');
        var inEditMode = $('.newLead').hasClass('inEditMode');
        var searchMade = $('#email').hasClass('popuplatedemail');
        var customerId = $("#customerId").attr('customerId');
        customerId = parseInt(customerId);
        if(window.AppointmentType == 1)
        {}
        else{
          validation();
          if(window.validState == false)
          {
            return false;
          }
        }
        $('#email').next().addClass('opacity0').next('.requiredError').addClass('opacity0');
        var dataCustomer = getCustomerValues();                   // Customer form values
        var dataLead = getLeadValues();                           // Lead form values
        var dataAppointment = getAppointmentValues();             // Appointment form values
        if(customerId != 0)
          { dataLead.customer_id = customerId }
        
        if(window.saveAndBook == true && searchMade == false) // Incase of Save and Book From New Lead
        {
            // Creating New Customer

                  $.ajax({
                    type: "POST",
                    url: "/ajaxCreateCustomerDashboard",
                    data: dataCustomer, 
                    success: function (data) { // return  customerID
                        var parsed = '';          
                        try{
                            parsed = JSON.parse(data); 
                            console.log(parsed);
                        }                 
                        catch(e)                
                        { return false; }

                        dataLead.customer_id = parsed
                        // After Customer created, create a lead for that customer
                        $.ajax({
                            type: "POST",
                            url: "/ajaxCreateLeadFromDashboard",
                            data: dataLead, 
                            success: function (data) { // returs lead Id
                                var parsed = '';          
                                try{
                                    parsed = JSON.parse(data); 
                                    console.log(parsed);
                                }                 
                                catch(e)                
                                { return false; }
                                $('.thisLeadId').attr('leadid', parsed);
                              
                            }
                        });

                    }
                });

        }
        else if(window.saveAndBook == true && searchMade == true)  // Incase of Save and Book From Search New Lead 
        {
          $.ajax({
                  type: "POST",
                  url: "/ajaxCreateLeadFromDashboard",
                  data: dataLead, 
                  success: function (data) { // returs lead Id
                      var parsed = '';          
                      try{
                          parsed = JSON.parse(data); 
                          console.log(parsed);
                      }                 
                      catch(e)                
                      { return false; }
                      $('.thisLeadId').attr('leadid', parsed);
                      $('#email').removeClass('popuplatedemail');
                      $(".hideOnSavenBook").addClass('hide');
                      $(".calendarShowOnBook").removeClass('hide'); 
                      return false;
                  }
              });
           
        }
        else if(window.AppointmentType == 1)  // Incase of Appointment From Room Manager
        {
            $.ajax({
                type: "POST",
                url: "/dashboard/ajaxSaveAppointment",
                data: dataAppointment, 
                success: function (data2) {
                    var parsed2 = '';          
                    try{  parsed2 = JSON.parse(data2); }                 
                    catch(e)                
                    { return false; }     
                    showMainLoading();
                    var getAssigneeId = window.selectedAssigneeId;
                    var getWeeklyDate = $('.calendarLoad .calendarWeeklyDate').attr('startdate');
                    loadQuestionViewcalnder(getAssigneeId, getWeeklyDate);
                    $('.thisLeadId').attr('leadId','');
                    getSearchData()
                    console.log('Appointment Saved');
                    return false;
                }
              });
        }
        else if(inEditMode) // Incase of Edit Lead
        {
              $.ajax({
                type: "POST",
                url: "/dashboard/ajaxAddDashboard",
                data: dataLead, 
                success: function (data) {
                    var parsed = '';          
                    try
                    { parsed = JSON.parse(data); }                 
                    catch(e) { return false; }
                    $.ajax({
                      type: "POST",
                      url: "/dashboard/ajaxSaveAppointment",
                      data: dataAppointment, 
                      success: function (data2) {
                          var parsed2 = '';          
                          try{ parsed2 = JSON.parse(data2); }                 
                          catch(e)                
                          { return false; }
                          if(parsed2.insertedId == 0)
                          {
                            var updatedBooking =  { lead_id : parsed2.lead_id , booking_date : parsed2.booking_date }
                            $.ajax({
                              type: "POST",
                              url: "/dashboard/ajaxUpdateDashboard",
                              data: updatedBooking, 
                              success: function (data3) {
                                var checkData = data3;
                              }
                            }); 
                          }
                          loadMainDashboardAfterSaveLead();
                          $('.thisLeadId').attr('leadId','');
                          return false;
                      }
                    });
                }
            });  
        }
        else
        {
            if(dataAppointment.lead_id == "") // Incase of Saved lead only from New Lead Form
            {
                // Creating New Customer
                if(customerId == 0)
                {
                $.ajax({
                      type: "POST",
                      url: "/ajaxCreateCustomerDashboard",
                      data: dataCustomer, 
                      success: function (data) { // return  customerID
                          var parsed = '';          
                          try{
                              parsed = JSON.parse(data); 
                              console.log(parsed);
                          }                 
                          catch(e)                
                          { return false; }

                          dataLead.customer_id = parsed
                          // After Customer created, create a lead for that customer
                          $.ajax({
                              type: "POST",
                              url: "/ajaxCreateLeadFromDashboard",
                              data: dataLead, 
                              success: function (data) { // returs lead Id
                                  var parsed = '';          
                                  try{
                                      parsed = JSON.parse(data); 
                                      console.log(parsed);
                                  }                 
                                  catch(e)                
                                  { return false; }
                                  loadMainDashboardAfterSaveLead();
                                  $('.thisLeadId').attr('leadId','');
                                  return false; 
                              }
                          });

                      }
                  });
                }
                else
                {
                  $.ajax({
                      type: "POST",
                      url: "/ajaxCreateLeadFromDashboard",
                      data: dataLead, 
                      success: function (data) { // returs lead Id
                          var parsed = '';          
                          try{
                              parsed = JSON.parse(data); 
                              console.log(parsed);
                          }                 
                          catch(e)                
                          { return false; }
                          loadMainDashboardAfterSaveLead();
                          $('.thisLeadId').attr('leadId','');
                          return false; 
                      }
                  });
                }

            }
            else if(searchMade == true) // Update Lead which comes from Book And Save
            {
              $.ajax({
                  type: "POST",
                  url: "/ajaxCreateLeadFromDashboard",
                  data: dataLead, 
                  success: function (data) { // returs lead Id
                      var parsed = '';          
                      try{
                          parsed = JSON.parse(data); 
                          console.log(parsed);
                      }                 
                      catch(e)                
                      { return false; }
                      loadMainDashboardAfterSaveLead();
                      $('.thisLeadId').attr('leadId','');
                      return false;
                    
                  }
              });
            }
            else  // Update Lead which comes from Save Only
            {
                $.ajax({  
                type: "POST",
                url: "/dashboard/ajaxSaveAppointment",
                data: dataAppointment, 
                success: function (data2) {
                    var parsed2 = '';          
                    try{ parsed2 = JSON.parse(data2); }                 
                    catch(e) { return false; }     
                    loadMainDashboardAfterSaveLead();
                    $('.thisLeadId').attr('leadId','');
                    return false;                      
                }
              });
            }
        }
      
      //Ajax Call Saving Lead
          
      return false;
      //End Ajax Call

    });

    function loadMainDashboardAfterSaveLead()
    {
      $('.rings a').removeClass('active');
      $('.rings a:last-child').addClass('active');
      loadLeads();
      //Setting header changes
      showMainLoading();
      $('.newLeaveContainer').hide();
      $('.newLead').addClass('maxHeightHide');
      $('.dashboardContainer').addClass('hide');
      $('.leavesContainer').addClass('hide');
      $('.leadsContainer').removeClass('hide');
      $('.new-Lead').removeClass('active');
      $('.dashboard-header').removeClass('hide');
      //Reset New lead form
      $('.newLead').html(window.getNewLeadAll);
      $('.newLead').removeClass('inEditMode');
      $('.thisLeadId').attr('leadid', '');    
      $('#appointmentId').attr('appointmentId','0');
      getSearchData();
    }

    /* ==================================================== */
    /* ================== End Save Form =================== */
    /* ==================================================== */


    $('.newLead').click(function(){
      //alert('a');
    });

    $(document).keypress(function(e) {
      if($('.newLead').hasClass('opened'))
      {
        if (e.which == 13) {
          $('.additional-details').slideDown(300);  
          $('.btn-nextDetails').addClass('hide');
          $('.btn-saveDetails, .btn-bookNow').removeClass('hide');
          //$('.next-saveDiv').addClass('one-half-pad-top');
          $('.newLead').removeClass('opened');
          $('#calendar').fullCalendar("destroy");
          $('.btn-cancel').removeClass('gap-right').addClass('triple-gap-right');
          $('.additional-details').addClass('opened');
          return e.which !== 13;
        }
      }
      if($('.additional-details').hasClass('opened'))
      {
        if (e.which == 13) {
          window.saveAndBook = false;
          $('#submitbutton').trigger('click');
          return e.which !== 13;
        }
      }
    });

    // Calcelling form submission on enter press
    $('form input').on('keypress', function(e) {
          return e.which !== 13;
    });
    
    $("form[type=text]").keypress(function(e) {
      if (e.which == 13) {
        return false;
      }
    });
    // Cancel Additional Details Button and reset

    $(document).on('click','.btn-cancel', function (e) {
        //$('.newLead').addClass('hide');

        //Setting header changes
        $('.newLeaveContainer').hide();
        $('.newLead').addClass('maxHeightHide');
        $('.dashboardContainer').addClass('hide');
        $('.leavesContainer').addClass('hide');
        $('.leadsContainer').removeClass('hide');
        $('.new-Lead').removeClass('active');
        $('.dashboard-header').removeClass('hide'); 

        //Reset New lead form
        $('.newLead').html(window.getNewLeadAll);
        $('.emailexists').addClass('opacity0').removeClass('green');
        $('.redCross, redGreen').addClass('hide');
    });


    // Question view calendar confirmation
    $(document).on('click','.Confirmation ', function (e) {
        //$('.newLead').addClass('hide');

        //Setting header changes
        $('.newLeaveContainer').hide();
        $('.newLead').addClass('maxHeightHide');
        $('.dashboardContainer').addClass('hide');
        $('.leavesContainer').addClass('hide');
        $('.leadsContainer').removeClass('hide');
        $('.new-Lead').removeClass('active');
        $('.dashboard-header').removeClass('hide');

        //Reset New lead form
        $('.newLead').html(window.getNewLeadAll);
        
    });

    // -----------------------------------------------------    
    
/* ------------------ Start Edit Detail ------------------------*/
/* ------------------ Start Edit Detail ------------------------*/
/* ------------------ Start Edit Detail ------------------------*/
    
    // Calling Edit function


    $(document).on('click','.editDetails:not(.disabled)', function (e) {

        $('.newLead').addClass('inEditMode');
        $('.saveNewBookingForNewLead').removeClass('hide');
        var getLeadId = $(this).attr('lead-id');    
        EditLead(getLeadId)
                   
    });

    $(document).on('click','.calendarLoad #NewCalendar .daysContent .roomBooking:not(.calendarLoad #NewCalendar .daysContent.pastdate .roomBooking)', function (e) {
      $('.addBookingPopup').html('');
      $('.addBookingPopup').addClass('hide');
      $('.roomBooking').removeClass('editable');
      $(this).addClass('editable');
      $('.dialogeBox.editBookingAppointment').removeClass('hide');
      e.preventDefault();
      return false;   
    });

    $(document).on('click','.yesEditBooking', function (e) {
      $('.roomBooking').removeClass('hide');
      $('.roomBooking.editable').addClass('hide');
      $('.dialogeBox.editBookingAppointment').addClass('hide');
      $('.loadnewCalendarContent').addClass('editable');
      var getLeadId = $('.roomBooking.editable').attr('lead-id');
      $('.thisLeadId').attr('leadid',getLeadId);
      window.editAppointmentId = $('.roomBooking.editable').attr('appointmentid');
      $('#appointmentId').attr('appointmentid', window.editAppointmentId);
      window.customerName = $('.roomBooking.editable p.headBar span.ellipsis').html();
      window.ownerID = $('.roomBooking.editable').attr('ownerID'); 
      window.ownerName = $('.roomBooking.editable').attr('ownername');
      window.budget = $('.roomBooking.editable').attr('budget');
      window.howheard = $('.roomBooking.editable').attr('refferal');
      $('.additional-details .dropdown.budget .dropdownOptions li a[value="'+window.budget+'"]').click();
      $('.requirements').val($('.roomBooking.editable').attr('requirements'));
      $('#onlyReferral').val($('.roomBooking.editable').attr('onlyReferral'));
      $('#referrenceDropdown').val($('.roomBooking.editable').attr('referenceProduct'));
      $('.additional-details .dropdown.referral .dropdownOptions li a[value="'+window.howheard+'"]').click();
      $('#specialinstructions').val($('.roomBooking.editable').attr('instructions'));
      
      e.preventDefault();
      return false;
    });

    $(document).on('click','.NoEditBooking', function (e) {
      $('.loadnewCalendarContent').removeClass('editable');
      $('.roomBooking').removeClass('editable').removeClass('hide');
      $('.dialogeBox.editBookingAppointment').addClass('hide');
      $('.loadnewCalendarContent').removeClass('editable');
      $('.roomBooking.editable').attr('lead-id','');
      e.preventDefault();
      return false;
    });

    
    function EditLead(getLeadId)
    {
        $('.searchArea').addClass('hide');
        $(".calendarShowOnBook").removeClass('hide'); 
        $(".savenBookOptions, .cancelNewBooking").addClass('hide');
        var getAssigneeId = window.selectedAssigneeId;
        startCalendarLoading();
        var getWeeklyDate = $('.calendarWeeklyDate').attr('startdate');
        
        loadQuestionViewcalnder(getAssigneeId, getWeeklyDate);

      $.ajax({
                type: "GET",
                url: "/dashboard/ajaxGetLeadDetailForLeadPage",
                data: {leadId:getLeadId},
                success: function (data) {
                  var parsed = '';
                  try
                  {
                    parsed = JSON.parse(data);                  
                  }
                  catch(e)
                  {                  
                   return false;                    
                  }
                  showMainLoading();
                  var html = "";
                 
                  // Get User Color
                  var getThisUserId = parsed.assign_to_UserId;
                  $.ajax({

                    type: "GET",
                    url: "/dashboard/ajaxGetUserColor",
                    data: {user_id : getThisUserId},
                    success: function (data) {
                      var parsed2 = '';          
                      try{                           
                        parsed2 = JSON.parse(data);              
                      }                 
                      catch(e)                
                      {                  
                        return false;                  
                      }
                      
                      window.userColor = '#'+parsed2["0"].color;
                      
                    }
                  });
                  
                  //Setting Lead Id
                  
                  $('.thisLeadId').attr('leadId',parsed.id);
                  //$('#email').addClass('popuplatedemail');
                  $("#appointmentId").attr('appointmentid',parsed.appointment_id)
                  
                  // Basic Info Fields
                  // Title
                  $('.basicInfo .title .selected-text').attr('value',parsed.title);
                  $('.basicInfo .title .selected-text span').html(parsed.title);
                  // Gender
                  $('.basicInfo .Gender .selected-text').attr('value',parsed.gender);
                  $('.basicInfo .Gender .selected-text span').html(parsed.gender);
                  // First Name Last Name Phone & Email
                  $('.basicInfo .firstname').val(parsed.first_name);
                  $('.basicInfo .lastname').val(parsed.last_name);
                  $('.basicInfo .phonenumber').val(parsed.phone_number);
                  $('#email').val(parsed.email);
                  $('#onlyReferral').val(parsed.only_referral);
                  $('#fullAddress').val(parsed.full_address);
                  
                  // Address Fields
                  $('.stateDiv .dropdown.State .dropdownOptions li a[value="'+parsed.State+'"]').trigger('click');
                  // Additional Detail Fields
                  //$("#productDropdown").html(parsed.product);
                  //$('#productDropdown').closest('a.selected-text').attr('value',parsed.product);
                  //$('#productDropdown').closest('a.selected-text').attr('shortcode',parsed.product_shortcode);

                  $('.dropdown.dropdown .dropdownOptions li a[value="'+parsed.product+'"]').click();

                  // Referral method
                  
                  if(parsed.referral != "Google" && parsed.referral != "Word of mouth" && parsed.referral != "Previous client" && parsed.referral != "Walk In" && parsed.referral != "Facebook" && parsed.referral != "Instagram")
                  { 
                    $('#referralDropdownOther').val(parsed.referral);
                    $('#referralDropdownOther').closest('.relative').removeClass('hide'); 
                    $('.additional-details .dropdown.referral .dropdownOptions li a[value="Other"]').trigger('click');
                  }
                  else
                  {
                    $('#referralDropdownOther').closest('.relative').addClass('hide'); 
                    $('.additional-details .dropdown.referral .dropdownOptions li a[value="'+parsed.referral+'"]').trigger('click');
                  }

                  $('.additional-details .instructions').val(parsed.special_instructions);
                  $('.additional-details .ReferenceProduct').val(parsed.reference_product);

                  $('.additional-details .dropdown.budget .dropdownOptions li a[value="'+parsed.budget+'"]').click();
                  $('.additional-details .dropdown.budget .dropdownOptions').hide();

                  $('#BudgetText').val(parsed.budget);
                  $('#BudgetText').focusin();
                  $('#BudgetText').focusout();

                  // Preferred method
                  if(parsed.contact_method != "Phone/Email" && parsed.contact_method != "Phone" && parsed.contact_method != "Email")
                  { 
                    $('#perferrefDropdownOther').val(parsed.contact_method);
                    $('#perferrefDropdownOther').closest('.relative').removeClass('hide'); 
                    $('.dropdown.preferredMethod .dropdownOptions li a[value="Other"]').trigger('click');
                  }
                  else
                  {
                    $('#perferrefDropdownOther').closest('.relative').addClass('hide'); 
                    $('.dropdown.preferredMethod .dropdownOptions li a[value="'+parsed.contact_method+'"]').trigger('click');
                  }
                  
                  // Communication method
                  $('.dropdown.CommunicationMethod .dropdownOptions li a[value="'+parsed.communication_method+'"]').trigger('click');

                  $('.additional-details .requirements').val(parsed.specify_requirements);
                   
                  $('.initialScreen').removeClass('hideshow');
                   
                   
                   $('#countryName').attr('value',parsed.country);
                   if(parsed.country == 'Australia')
                   {
                    $('.stateDiv').removeClass('hide');
                   }
                   else
                   {
                    $('.stateDiv').addClass('hide');
                   }
                   //GetCountriesList();
                   $('#combobox').html('');
                   $('#combobox').html(window.comboboxList);
                   $(function() {
                      $("#combobox").combobox({
                          selected: function(event, ui) {
                              $('#log').text('selected ' + $("#combobox").val());
                          }
                      });
                      $("#combobox").next().next('.ui-combobox').remove();
                      
                      //$("#combobox").closest(".ui-widget").find("input, button").prop("disabled", true);
                  });
                   $('.ui-state-default, .ui-autocomplete-input').val(parsed.country);
                  setTimeout(function(){ 
                      
                      $('.additional-details .dropdown.assignToDiv .dropdownOptions li a[id="'+parsed.assign_to_UserId+'"]').click();
                      $('.additional-details .dropdown.assignToDiv .dropdownOptions').hide();
                      $('.additional-details .dropdown.assignToDiv .dropdownOptions .btn-skip2').click();
                      
                      // Check to see is user is other than super user and hiding assign to 
                                        
                      if(window.adminUser == true)
                      {
                        $('#BudgetText').closest('div.relative').removeAttr('readonly'); 
                        $('.hideBudget').addClass('hide');
                        
                        if(parsed.reson_skip_next_in_line != "")
                        {
                            $('.assignToDiv').closest('div.relative').removeClass('hide');
                            $('.otherReasonDiv').removeClass('hide');
                            $('.otherReasonDiv a.selected-text').attr('value',parsed.reson_skip_next_in_line);
                            $('.otherReasonDiv a.selected-text span').html(parsed.reson_skip_next_in_line);
                        }
                       }
                      else
                      { 
                        $('#BudgetText').closest('div.relative').attr('readonly','readonly');
                        $('.assignToDiv').closest('div.relative').addClass('hide');
                        $('.otherReasonDiv').addClass('hide');
                        $('.hideBudget').removeClass('hide');
                       }

                      // Booking Calendar
                      
                      //var getAmPm = parsed[0].booking_timezone;
                      var getFullDate = parsed.booking_date;
                      var getAssigneeId = parsed.assign_to_UserId;
                      var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
                      var getDayName = new Date(getFullDate);
                      var getDay = weekday[getDayName.getDay()];

                      var d = new Date(getFullDate);
                      var getOnlyDate = d.getDate();
                      // Get month name
                      var getMonth = d.getMonth() + 1;
                      // Get Current Month Dates
                      var m_names = ['January', 'February', 'March', 
                                       'April', 'May', 'June', 'July', 
                                       'August', 'September', 'October', 'November', 'December'];
                      //month name
                      var getMonth = m_names[d.getMonth()];
                      var getCurrentDate = d.getDate();
                      
                        
                      $('.suggestedDate').html('');
                      //$('.questionView').removeClass('hide');
                      $('.btn-saveBooking').addClass('saveNow');
                      // destroy calendar
                      $('#calendar2').fullCalendar("destroy");

                      //$('.next-saveDiv').addClass('one-half-pad-top');

                      
                      $('#bookingDate').addClass('nowCanSave');
                      
                      $('.btn-bookNow open, .bookNowMain').addClass('hide');
                      $('.savedBooking, .next-saveDiv').removeClass('hide');
                      var getThisDate = parsed.booking_date;
                      loadWeeklyDates(getThisDate);
                      $('.btn-bookNow').addClass('hide');

                      var monthname = $('#bookingDate').attr('monthNameEdit');
                      var bookingyear = $('#bookingDate').attr('bookingyear');
                      var monthnumber = $('#bookingDate').attr('monthnumber');
                      var timeslot = parsed.booking_time;
                      var getFullTime = getTimeSlotFull(timeslot);
                      var timeslotfull = getFullTime;
                      var getBookingDay = getDay;
                      var datenumber = getCurrentDate;
                      if(datenumber < 10)
                        { datenumber = '0'+datenumber; }

                      var getSuffixDate = getSuffix3(datenumber);
                      
                      var datenumberSuffix = datenumber+getSuffixDate;
                      var roomnumber = parsed.booking_room; 
                      var comlpetedate = parsed.booking_date;

                      $('#bookingDate').attr('timeslot',timeslot);
                      $('#bookingDate').attr('timeslotfull',timeslotfull);
                      $('#bookingDate').attr('dayname',getBookingDay);
                      $('#bookingDate').attr('datenumber', datenumber);
                      $('#bookingDate').attr('roomnumber',roomnumber);
                      $('#bookingDate').attr('comlpetedate', comlpetedate);
                      $('#bookingDate').attr('bookingstart', parsed.bookingstart);
                      $('#bookingDate').attr('durationtime', parsed.durationTime);
                      
                      
                      
                      var getStartingHour = getTime(parsed.booking_time);
                      bookingTimeDuration(parsed.durationTime, parsed.bookingstart, getStartingHour);
                      var getUpdatedTimeFull = $('#bookingDate').attr('updatedTime');
                      var setHtml = getBookingDay + ' ' + datenumberSuffix + ' ' + monthname + ', ' + bookingyear + ' ' + getUpdatedTimeFull;
                      $('#bookingDate').html(setHtml);
                      
                      
                      
                  }, 1000);
                  

                  $('.leadDeailContainer').addClass('hide');
                  $('.newLead').removeClass('maxHeightHide');
                  $('.dashboard-header').addClass('hide');
                  $('.basicInfo div.relative span, .additional-details div.relative span:first-child').css('display','inline-block');
                  $('.addressContainer, .additional-details').show();
                  $('.btn-nextDetails').addClass('hide');
                  $('.btn-saveDetails').removeClass('hide');
                  if(parsed.user_booking_date == '1')
                  {
                    $('.bookNowDiv').removeClass('hide'); 
                  }
                  else
                  {
                    $('.next-saveDiv').removeClass('hide');
                  }    
                  $('.calendarWeeklyDate').attr('bookingDate', parsed.booking_date); 
                  $('.newLead').removeClass('hide');
                  $('.calendarLoad').addClass('hide');
                  $('ul.dropdownOptions').hide();
                }
            }); 
    }


    
/* ------------------ End Edit Detail ------------------------*/
/* ------------------ End Edit Detail ------------------------*/
/* ------------------ End Edit Detail ------------------------*/

/*------------------------------------------------------------------*/
/*--------------------- --Load Question View Calender Start ------- */
/*------------------------------------------------------------------*/

    function myFunction()
    {
      //alert('ok');
    }
    function loadQuestionViewcalnder(getAssigneeId, getWeeklyDate)
    { 


          // Checking if Agent selected is on leave
          
          $(".daysContent").removeClass('agentOnLeave').attr('title',"");
          var data2 =  {booking_date : getWeeklyDate , assign_UserId : getAssigneeId}

          if(getAssigneeId == undefined || getAssigneeId == null)
          {
            //console.log('yes');
          }
          else
          {
            $.ajax({
              type: "GET",
              url: "/dashboard/ajaxGetUserLeaves",
              data: data2, 
              success: function (data) {
                  var parsed = '';
                  try{
                    parsed = JSON.parse(data);
                  }
                  catch(e)
                  {
                    return false;                  
                  } 
                  var weeklyDatesArray = [];
                  dateCounter = 0;
                  $.each(parsed, function(key, value){
                    var getkey = value.isOnLeave; 
                    if(getkey == '1')
                    {
                      weeklyDatesArray.push(value.Day);  
                    }
                  });
                  var  getAgent = $('.assignToDiv a.selected-text').attr('value');
                  var setDays = "";
                  var getArrayLength = weeklyDatesArray.length;
                  if(getArrayLength == 1)
                  {
                    setDays = weeklyDatesArray[0];
                  }
                  else if(getArrayLength == 2)
                  {
                    setDays = weeklyDatesArray[0] + ' & ' + weeklyDatesArray[1];
                  }
                  else if(getArrayLength == 3)
                  {
                    setDays = weeklyDatesArray[0] + ', ' + weeklyDatesArray[1] + ' & ' + weeklyDatesArray[2];
                  }
                  else if(getArrayLength == 4)
                  {
                    setDays = weeklyDatesArray[0] + ', ' + weeklyDatesArray[1] + ', ' + weeklyDatesArray[2] + ' & ' + weeklyDatesArray[3];
                  }
                  else if(getArrayLength == 5)
                  {
                    setDays = weeklyDatesArray[0] + ', ' + weeklyDatesArray[1] + ', ' + weeklyDatesArray[2] + ', ' + weeklyDatesArray[3] + ' & ' + weeklyDatesArray[4];
                  }
                  else
                  {
                    setDays = weeklyDatesArray[0] + ', ' + weeklyDatesArray[1] + ', ' + weeklyDatesArray[2] + ', ' + weeklyDatesArray[3] + ', ' + weeklyDatesArray[4] + ' & ' + weeklyDatesArray[5];
                  }
                  var setMessage = getAgent + ' is on leave on ' + setDays+'.';
                  $('.dialogeBox.leaveCheck .boxmessage').html(setMessage);
                  if(getArrayLength > 0)
                  {
                    $('.dropdown.assignToDiv').find('a.selected-text').attr('value',"All");
                    $('.dropdown.assignToDiv').find('a.selected-text span').html('*Assign to');
                    $('.dialogeBox.leaveCheck').removeClass('hide');
                      setTimeout(function(){ 
                        $('.dialogeBox.leaveCheck').addClass('hide');
                    }, 3000); 
                  }   
              }
            }); 
          }
          
           
          var data = {booking_date : getWeeklyDate , assign_UserId : getAssigneeId}
          // Get 
          $.ajax({
                
                type: "GET",
                url: "dashboard/ajaxGetDataForCustomViewCalender",
                data: data,
                success: function (data) {
                // Convert Json into Array

                var parsed = '';
                try{
                  parsed = JSON.parse(data);
                }
                catch(e)
                {
                  return false;                  
                }   
                
                // Get Weekly Start Dates 

                /*================================*/
                var checkLeadId = $('.thisLeadId').attr('leadId');
                var currentDate = '';
                if(checkLeadId != "")
                  { currentDate = new Date(); }
                else
                  { currentDate = new Date(); }

                
                var day = currentDate.getDate();
                if(day < 10){day = '0'+ day}
                var month = currentDate.getMonth() + 1;
                if(month < 10){month = '0'+ month}
                var year = currentDate.getFullYear();
                var currentTime = year + "-" + month + "-" + day;

                // Check if it is Edit or new lead
                var checkLeadId = $('.thisLeadId').attr('leadId');
                //var currentTime = new Date();
                var dateCounter = 0;
                var weeklyDatesArray = [];
                var getCurrentFullDate = $('.calendarWeeklyDate').attr('currentdaydate');
                $.each(parsed, function(key, value){

                  dateCounter++;
                  var getkey = key;
                  weeklyDatesArray.push(getkey);
                  // get Day by Name
                  var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
                  var a = new Date(getkey);
                  var getDayName = weekday[a.getDay()];
                  // get Day Date
                  var dayDate = a.getDate();
                  // get Month
                  var getMonth = a.getMonth()+1;

                  var setFinalDate = getDayName + ' ' + dayDate + '/' + getMonth;
                  $('.daysCalendar.'+dateCounter + ' .headerPart').html(setFinalDate);
                  $('.daysCalendar.'+dateCounter + ' .headerPart').attr('keyDates',key);

                  if(checkLeadId != "")
                  {

                  }
                  else
                  {
                    if(currentTime == key)
                    {
                      $('.daysCalendar.'+dateCounter + ' .headerPart').addClass('activeColor');
                    }
                  }
                  
                  if(getCurrentFullDate > key)
                  {
                    $('.daysContent.'+dateCounter).attr('dateNumber', dayDate).addClass('pastdate').attr('title',"Booking cannot be made on past date");
                    $('.daysContent.sundayContent.'+dateCounter).removeClass('pastdate').attr('title',"");
                   
                    $('.daysContent.'+dateCounter).attr('fullDate', key);
                  }
                  else
                  {
                    $('.daysContent.'+dateCounter).attr('dateNumber', dayDate).removeClass('pastdate').attr('title',"");
                    $('.daysContent.'+dateCounter).attr('fullDate', key);
                  }
                  
                 
                });

                // Get Weekly End Dates 

                /*================================*/
                
                // Bind Data to  Weekly Start Dates 
                
                // Marray array for data
                
                var arr = [];
                for(var x in parsed){
                  arr.push(parsed[x]);
                }
                
                var currentCounter = 0;
                var newCounter = 0
                // Getting into each weekly date
                for (var i = 0; i < 7; i++) {
                  currentCounter++;
                  var dayTimes = arr[i];
                  var setDivContainer = "";

                  // Getting into each weekly date Times
                  
                  $.each(dayTimes, function(key, value){
                    window.onLeaveCheck = false;
                    newCounter++;
                    var getKey = getTime(key);
                    var chcekKey = key;
                    if(chcekKey == '100') 
                    {
                      var checkPreviousCounter = currentCounter;
                      //checkPreviousCounter--;
                      var setLeaveData = dayTimes[key].Leave_AssignUserName + " is on leave";
                      $(".daysContent."+[checkPreviousCounter]).addClass('agentOnLeave').attr('title',setLeaveData);
                      window.onLeaveCheck = true;
                      //return false;
                    }
                    else
                    {
                      window.onLeaveCheck = false;
                      //$(".daysContent."+[currentCounter]).removeClass('agentOnLeave').attr('title',"");
                    }
                    setDivContainer = ".daysContent."+[currentCounter] + " .daysContentSlider." + getKey;
                    var getAllRooms = dayTimes[key];

                    // Setting html for binding bookings
                    var setThisHtml = '<div class="roomsContainer relative transition-ease-02">';
                    // Getting into each weekly date, times and then Rooms
                    var newRoomCounter = 1;
                    var roomNumber = "";
                    var positionLeft = "";
                    if(window.onLeaveCheck == false)
                      {
                        for (var i = 1; i < 5; i++) {
                          
                          if(newRoomCounter == 1){roomNumber = "roomOne"; positionLeft = 'roomOnePos'}
                          else if(newRoomCounter == 2){roomNumber = "roomTwo"; positionLeft = 'roomTwoPos'}
                          else if(newRoomCounter == 3){roomNumber = "roomThree"; positionLeft = 'roomThreePos'}
                          else{roomNumber = "roomFour"; positionLeft = 'roomFourPos'}
                          var getRoom = getAllRooms[i];
                          

                          if(getRoom == "" || getRoom == null)
                          {
                            //window.userColor = '#D3D3D3';
                            setThisHtml +='<label class="labelContainer '+positionLeft+'" roomNumber="'+i+'">';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="0" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="15" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="30" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="45" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='</label>';
                          }
                          else
                          {
                            
                            //window.userColor = '#'+getAllRooms[i].booking_color;
                            
                            // Seeting booking Html
                            // setThisHtml +='<label class="labelContainer '+positionLeft+'" roomNumber="'+i+'" duration="'+getDurationTime+'" bookingstart="'+getAllRooms[i][Key].bookingstart+'"   >';
                            
                            setThisHtml +='<label class="labelContainer '+positionLeft+'" roomNumber="'+i+'" >';

                            setThisHtml +='<div class="addBookingLinkContent">';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="0" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="15" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="30" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='<p class="newBookingBorder"><a class="addBookingLink" bookingStart="45" href="javascript:;"><i class="icon-addBookingLinkNew fs-12"></i></a></p>';
                            setThisHtml +='</div>';


                            var Key = 0;
                            $.each(getAllRooms[i], function(key, value){
                              
                            
                            var getDurationTime = getAllRooms[i][key].durationTime;
                            var getBookingstartTime = getAllRooms[i][key].bookingstart;
                            var height1 = '';
                            var height2 = '';
                            var positionTop = '';

                            if(getDurationTime != null)
                            {
                              if(getDurationTime == '15') { height1 = '16px'; height2 = '0px'; }
                              else if(getDurationTime == '30') { height1 = '33px'; height2 = '17px'; }
                              else if(getDurationTime == '45') { height1 = '48px'; height2 = '33px'; }
                              else if(getDurationTime == '60') { height1 = '65px'; height2 = '49px'; }
                              else if(getDurationTime == '75') { height1 = '80px'; height2 = '65px'; }
                              else{ height1 = '97px'; height2 = '81px'; }
                              
                            }
                                                
                            // Setting New Booking Link Loop newBookingLink

                              // room booking
                              if(getBookingstartTime != null)
                              {
                                if(getBookingstartTime == '0') { positionTop = '0px'; }
                                else if(getBookingstartTime == '15') { positionTop = '15px';}
                                else if(getBookingstartTime == '30') { positionTop = '31px';}
                                else if(getBookingstartTime == '45') { positionTop = '47px';}
                              }

                              var setBackgroundColor = "style='background-color:"+getAllRooms[i][key].color+"'";
                              var setBackgroundColorHeight = 'style="background-color:'+getAllRooms[i][key].color+'; height:'+height2+'"';
                              var Color = "style='color:"+getAllRooms[i][key].color+"'";
                                var checkOtherLead = getAllRooms[i][key].lead_id;
                                var thisIsOther =  false;
                                if(checkOtherLead == '90000')
                                {
                                  thisIsOther =  true;
                                }
                                var personBookingName = getAllRooms[i][key].first_name + ' ' + getAllRooms[i][key].last_name;
                                if(thisIsOther == true)
                                {
                                  personBookingName = getAllRooms[i][key].fullName;
                                }
                                setThisHtml +='<div class="roomBooking '+roomNumber+'" instructions="'+getAllRooms[i][key].special_instructions+'"  refferal="'+getAllRooms[i][key].referral+'" onlyReferral="'+getAllRooms[i][key].only_referral+'" referenceProduct="'+getAllRooms[i][key].reference_product+'" requirements="'+getAllRooms[i][key].specify_requirements+'" budget="'+getAllRooms[i][key].budget+'" ownerID="'+getAllRooms[i][key].assign_to_UserId+'" ownerName="'+getAllRooms[i][key].assign_to+'" appointmentId="'+getAllRooms[i][key].appointment_id+'"  lead-id="'+getAllRooms[i][key].id+'" topPosition="'+positionTop+'" style="height:' + height1 + '; top:'+ positionTop +'">';
                                setThisHtml +='<p class=" fs-11 headBar" '+setBackgroundColor+'><span class="ellipsis">' + personBookingName + '</span><span>'+getAllRooms[i][key].assignto_shortcode+'</span></p>';
                                setThisHtml +='<div class="full align-left half-pad-left lh-16 fs-11 one-pad-top relative">';
                                  setThisHtml +='<p><i class="icon-diamond fs-11 " '+Color+'></i> <span class=" d-i-b half-pad-left">'+getAllRooms[i][key].product_shortcode+'</span></p>';
                                  if(thisIsOther == false)
                                  {
                                    setThisHtml +='<p><i class="icon-dollar fs-11 " '+Color+'></i> <span class=" d-i-b half-pad-left">'+getAllRooms[i][key].budget+'</span></p>';
                                  }
                                  setThisHtml +='<div class="transparentBG absolute" '+setBackgroundColorHeight+' style="height:'+ height2 +'"></div>';
                                setThisHtml +='</div>';
                              setThisHtml +='</div>';

                              });


                              

                            setThisHtml +='</label>';
                          }
                          newRoomCounter++
                        }
                      }
                      
                    setThisHtml += '<p class="borderPart"></p>';
                    setThisHtml +='</div>';

                    $(setDivContainer).html(setThisHtml);

                  });

                }

                // Reset slider
                $('.daysSlider div.roomsContainer').removeClass('scrolled');
                // End Loading
                
                endCalendarLoading2();
                //endCalendarLoading();


            }
            
        });
    }

    function getTime(key)
    {
      if(key == "8-9")
        { return 8}
      if(key == "9-10")
        { return 9}
      if(key == "10-11")
        { return 10}
      if(key == "11-12")
        { return 11}
      if(key == "12-1")
        { return 12}
      if(key == "1-2")
        { return 1}
      if(key == "2-3")
        { return 2}
      if(key == "3-4")
        { return 3}
      if(key == "4-5")
        { return 4}
      if(key == "5-6")
        { return 5}

    }
    //Function Call on Window onLoad
        

    function addingArray(myArray, startDate, count2, arr, getAmPm)
    {
      if(getAmPm == "PM")
      {
        // Form AM
        // 12 - 1 AM
        if(arr[count2])
        {
          if(arr[count2]["12:00 - 1:00"])
            {

              myArray.push({
                title: '12:00 - 1:00',
                start: startDate,
                className: arr[count2]["12:00 - 1:00"].class
              });

            }
            else
            {

              myArray.push({
                title: '12:00 - 1:00',
                start: startDate,
                className: 'zero'
              });

            }

        }
        else
        {

          myArray.push({
            title: '12:00 - 1:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 1 - 2 AM
        if(arr[count2])
        {
          if(arr[count2]["1:00 - 2:00"])
          {

            myArray.push({
              title: '1:00 - 2:00',
              start: startDate,
              className: arr[count2]["1:00 - 2:00"].class
            });

          }
          else
          {

            myArray.push({
              title: '1:00 - 2:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {

          myArray.push({
            title: '9:00 - 10:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 2 - 3 AM
        if(arr[count2])
        {
          if(arr[count2]["2:00 - 3:00"])
          {

            myArray.push({
              title: '2:00 - 3:00',
              start: startDate,
              className: arr[count2]["2:00 - 3:00"].class
            });

          }
          else
          {

            myArray.push({
              title: '2:00 - 3:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {

          myArray.push({
            title: '2:00 - 3:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 3 - 4 AM
        if(arr[count2])
        {
           if(arr[count2]["3:00 - 4:00"])
          {
          
            myArray.push({
              title: '3:00 - 4:00',
              start: startDate,
              className: arr[count2]["3:00 - 4:00"].class
            });
          
          }
          else
          {
            
            myArray.push({
              title: '3:00 - 4:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {
          
          myArray.push({
            title: '3:00 - 4:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 4 - 5 AM
        if(arr[count2])
        {
           if(arr[count2]["4:00 - 5:00"])
          {
          
            myArray.push({
              title: '4:00 - 5:00',
              start: startDate,
              className: arr[count2]["4:00 - 5:00"].class
            });
          
          }
          else
          {
            
            myArray.push({
              title: '4:00 - 5:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {
          
          myArray.push({
            title: '4:00 - 5:00',
            start: startDate,
            className: 'zero'
          });

        }

      }
      else // For AM
      {
        /* 8 - 9 AM */
        if(arr[count2])
        {
          if(arr[count2]["8:00 - 9:00"])
            {

              myArray.push({
                title: '8:00 - 9:00',
                start: startDate,
                className: arr[count2]["8:00 - 9:00"].class
              });

            }
            else
            {

              myArray.push({
                title: '8:00 - 9:00',
                start: startDate,
                className: 'zero'
              });

            }

        }
        else
        {

          myArray.push({
            title: '8:00 - 9:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 9 - 10 AM
        if(arr[count2])
        {
          if(arr[count2]["9:00 - 10:00"])
          {

            myArray.push({
              title: '9:00 - 10:00',
              start: startDate,
              className: arr[count2]["9:00 - 10:00"].class
            });

          }
          else
          {

            myArray.push({
              title: '9:00 - 10:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {

          myArray.push({
            title: '9:00 - 10:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 10 - 11 AM
        if(arr[count2])
        {
          if(arr[count2]["10:00 - 11:00"])
          {

            myArray.push({
              title: '10:00 - 11:00',
              start: startDate,
              className: arr[count2]["10:00 - 11:00"].class
            });

          }
          else
          {

            myArray.push({
              title: '10:00 - 11:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {

          myArray.push({
            title: '10:00 - 11:00',
            start: startDate,
            className: 'zero'
          });

        }
        // 11 - 12 AM
        if(arr[count2])
        {
           if(arr[count2]["11:00 - 12:00"])
          {
          
            myArray.push({
              title: '11:00 - 12:00',
              start: startDate,
              className: arr[count2]["11:00 - 12:00"].class
            });
          
          }
          else
          {
            
            myArray.push({
              title: '11:00 - 12:00',
              start: startDate,
              className: 'zero'
            });

          }
        }
        else
        {
          
          myArray.push({
            title: '11:00 - 12:00',
            start: startDate,
            className: 'zero'
          });

        }
      }
    }

    function addingArrayForElse(myArray, startDate, getAmPm)
    {

      if(getAmPm == "AM")
        {
          // 8 - 9 AM
          myArray.push({
            title: '8:00 - 9:00',
            start: startDate,
            className: 'zero'
          });
          // 9 - 10 AM
          myArray.push({
            title: '9:00 - 10:00',
            start: startDate,
            className: 'zero'
          });
          // 10 - 11 AM
          myArray.push({
            title: '10:00 - 11:00',
            start: startDate,
            className: 'zero'
          });
          // 11 - 12 AM                         
          myArray.push({
            title: '11:00 - 12:00',
            start: startDate,
            className: 'zero'
          });
        }
      else
        {
          // 12 - 1 AM
          myArray.push({
            title: '12:00 - 1:00',
            start: startDate,
            className: 'zero'
          });
          // 1 - 2 AM
          myArray.push({
            title: '1:00 - 2:00',
            start: startDate,
            className: 'zero'
          });
          // 2 - 3 AM
          myArray.push({
            title: '2:00 - 3:00',
            start: startDate,
            className: 'zero'
          });
          // 3 - 4 AM                         
          myArray.push({
            title: '3:00 - 4:00',
            start: startDate,
            className: 'zero'
          });
          // 4 - 5 AM                         
          myArray.push({
            title: '4:00 - 5:00',
            start: startDate,
            className: 'zero'
          });          
        }

    }

    function addingLeaveArray(myArray, startDate, getAmPm)
    {
      if(getAmPm == "AM")
        {
          // 8 - 9 AM
          myArray.push({
            title: '8:00 - 9:00',
            start: startDate,
            className: 'onLeave'
          });
          // 9 - 10 AM
          myArray.push({
            title: '9:00 - 10:00',
            start: startDate,
            className: 'onLeave'
          });
          // 10 - 11 AM
          myArray.push({
            title: '10:00 - 11:00',
            start: startDate,
            className: 'onLeave'
          });
          // 11 - 12 AM                         
          myArray.push({
            title: '11:00 - 12:00',
            start: startDate,
            className: 'onLeave'
          });
        }
      else
        {
          // 12 - 1 AM
          myArray.push({
            title: '12:00 - 1:00',
            start: startDate,
            className: 'onLeave'
          });
          // 1 - 2 AM
          myArray.push({
            title: '1:00 - 2:00',
            start: startDate,
            className: 'onLeave'
          });
          // 2 - 3 AM
          myArray.push({
            title: '2:00 - 3:00',
            start: startDate,
            className: 'onLeave'
          });
          // 3 - 4 AM                         
          myArray.push({
            title: '3:00 - 4:00',
            start: startDate,
            className: 'onLeave'
          });
          // 4 - 5 AM                         
          myArray.push({
            title: '4:00 - 5:00',
            start: startDate,
            className: 'onLeave'
          });          
        }
        
    }

/*------------------------------------------------------------------*/
/*--------------------- --Load Question View Calender End --------- */
/*------------------------------------------------------------------*/



    // Full Calendar 2
    
    function loadCalendar2(getDay, getFullDate, getOnlyDate) {

        $('.daySelection').addClass('active');
        var CalendarDay = [];  

        if(getDay == "Monday")
        {   CalendarDay = [0, 2, 3, 4, 5, 6]}
        else if(getDay == "Tuesday")
        {   CalendarDay = [0, 1, 3, 4, 5, 6]}
        else if(getDay == "Wednesday")
            {CalendarDay = [0, 1, 2, 4, 5, 6]}
        else if(getDay == "Thursday")
            {CalendarDay = [0, 1, 2, 3, 5, 6]}
        else if(getDay == "Friday")
            {CalendarDay = [0, 1, 2, 3, 4, 6]}
        else{CalendarDay = [0, 1, 2, 3, 4, 5]}

        // Start O fcalendar

        // Get Month Name
        window.getFullDate = getFullDate;
        window.getDay = getDay;
        var date = new Date(getFullDate),
        locale = "en-us",
        monthName = date.toLocaleString(locale, { month: "long" });

        // initialize weekly calendar
        $('#calendar2').fullCalendar({

              defaultDate: getFullDate,
              header: { center: 'month, agendaWeek' },
              navLinks: true, // can click day/week names to navigate views
              editable: false,
              eventLimit: true, // allow "more" link when too many events
              hiddenDays: CalendarDay,
              dayNamesShort: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
              //events: eventing,
              events: window.questionViewCalendar,
              columnFormat: 'dddd',
              eventOrder: "start",
              eventClick: function(calEvent, jsEvent, view) {

                  if(calEvent.className == "three")
                  { 
                    var message = "All rooms are booked";
                    //showMessage(message)
                    $('.dialogeBox.leaveCheck .boxmessage').html(message);
                    $('.dialogeBox.leaveCheck').removeClass('hide');
                        setTimeout(function(){ 
                          $('.dialogeBox.leaveCheck').addClass('hide');
                    }, 2000);
                    return false;
                  }
                  if(calEvent.className == "onLeave")
                  { 
                    var getAssignee = $('.selectedAgent p.userName').text();
                    var message = getAssignee + " is on leave";
                    //showMessage(message)
                    $('.dialogeBox.leaveCheck .boxmessage').html(message);
                    $('.dialogeBox.leaveCheck').removeClass('hide');
                        setTimeout(function(){ 
                          $('.dialogeBox.leaveCheck').addClass('hide');
                    }, 2000);
                    return false;
                  }
                  
                  var getTime = $(this).find('.fc-title').text(); 
                  var getFullDate = $(this).closest('table').find('thead').find('.fc-day-top').attr('data-date');
                  var getSelectedDate = $(this).closest('table').find('thead').find('.fc-day-number').attr('value');
                  if(getTime == 'Not available' || getTime == 'Annual leave')
                  {
                      return false;
                  }
                  else
                  {
                      $('#calendar2 .fc-row .fc-event').removeClass('active');
                      $(this).addClass('active');

                      suggestedDateFromCalendar(getTime, getSelectedDate, getFullDate)
                      $('.meetingRoomValue').attr('value','Meeting Room 3');
                      $('#bookingDate').addClass('nowCanSave'); 
                  }
              }

        });

        setTimeout(function(){ $('.fc-day-number').removeAttr('data-goto'); }, 200);
        
        // Setting weekly calendar heading
        var index = 0
        $('#calendar2 .fc-day-number').each(function(){
            //$(this).appendTo(monthName);

            var setHtml = getDay + " " + $(this).text() + " " + monthName;
            $(this).attr('value',setHtml);
            var getThisDay = parseInt($(this).text());
            var getCurrentDay = parseInt($(this).text());

            $(this).attr('date',getThisDay);
            if(getThisDay < 10)
            {
                getThisDay = "0"+getThisDay;
            }

            // -----------------------------------------------------

            // Adding weeks texts

            if(index == 0)
            { $(this).html("<span class='dayTitleHeader robotomedium'>"+getDay+" this week</span>"+ " " ); }
            else if(index == 1)
            { $(this).html("<span class='dayTitleHeader robotomedium'>"+getDay+" next week</span>"+ " " ); }
            else if(index == 2)
            { $(this).html("<span class='dayTitleHeader robotomedium'>"+getDay+" 2 weeks</span>"+ " " ); }
            else if(index == 3)
            { $(this).html("<span class='dayTitleHeader robotomedium'>"+getDay+" 3 weeks</span>"+ " " ); }
            else
            { $(this).html("<span class='dayTitleHeader robotomedium'>"+getDay+" 4 weeks</span>"+ " " ); }

            // -----------------------------------------------------

            // Adding Suffix

            
            var suffix = ''
            if(getCurrentDay == 1 || getCurrentDay == 21 || getCurrentDay == 31)
            {
                suffix = 'st'
            }
            else if(getCurrentDay == 2 || getCurrentDay == 22)
            {
                suffix = 'nd'   
            }
            else if(getCurrentDay == 3 || getCurrentDay == 23)
            {
                suffix = 'rd' 
            }
            else
            {
                suffix = 'th'    
            }
            getCurrentDay = getCurrentDay + suffix;

            // -----------------------------------------------------

            
            $(this).append( " " + getCurrentDay + " " + monthName );
            index++
        });

        $('#calendar2 .fc-day-number').filter();

        //month

        // End of Calendar

    $('#calendar2 .fc-other-month').closest('.fc-row').remove();

    setTimeout(function(){ 
        $('#calendar2').find('.loading').remove();
        $('.coverAreaBooking').addClass('hide');
     }, 1000);

    
    }

    // Weekly calendar left click
    $(document).on('click','#calendar2 .fc-prev-button', function() {
        var getAmPm = $('.timeSelection a').filter('.active').attr('value');
        var getAssigneeId = window.selectedAssigneeId;
        loadingCalendar2();

        //$('.suggestedDate').html('');
        var getOnlyDate = getOnlyDates();
        var currentDate = moment(window.getFullDate);
        getFullDate = moment(currentDate).subtract(1, 'M').format('YYYY-MM-DD');
        var getDay = window.getDay;
        $('#calendar2').fullCalendar("destroy");
        setTimeout(function(){
           $('.coverAreaBooking').removeClass('hide');
            loadQuestionViewcalnder(getDay, getFullDate, getOnlyDate, getAssigneeId, getAmPm)
        }, 100);
      
        return false;
    });
    // Weekly calendar right click
    $(document).on('click','#calendar2 .fc-next-button', function() {
        var getAmPm = $('.timeSelection a').filter('.active').attr('value');
        var getAssigneeId = window.selectedAssigneeId;
        loadingCalendar2();
        //$('.suggestedDate').html('');
        var getOnlyDate = getOnlyDates();
        var currentDate = moment(window.getFullDate);
        getFullDate = moment(currentDate).add(1, 'M').format('YYYY-MM-DD');
        var getDay = window.getDay;
        $('#calendar2').fullCalendar("destroy");
        setTimeout(function(){ 
            $('.coverAreaBooking').removeClass('hide');
            loadQuestionViewcalnder(getDay, getFullDate, getOnlyDate, getAssigneeId, getAmPm)
        }, 100);
        
        return false;
    });

    function loadingCalendar2()
    {
        $('#calendar2').find('.loading').remove();
        var loadingHtml = '<div class="loading fullHeight align-center absolute z-index99 full top-0  left-0" style=""><img src="/images/loading.gif"></div>';
        $('#calendar2').append(loadingHtml);
    }
    function startCalendarLoading()
    {
        var loadingHtml = '<div class="loading fullHeight align-center absolute z-index99 full top-0  left-0" style=""><img src="/images/loading.gif"></div>';
        $('.NewCalendarContainer').append(loadingHtml);
    }
    
}); // Ending Document ready

// Getting agents List

var myArray = [];
var AgentList = []; 



// Expand Additional Detail Div
function additionalDetailsExpand() {
    $('.icExpand').addClass('hide');
    $('.icMinimize').removeClass('hide');
    $('.additionalDiv').slideDown(300);

}

// Show success error message
function showMessage(message) {
    $('.showMessage div').html(message);
    $('.showMessage').addClass('top0');
    setTimeout(function(){ 
        $('.showMessage').removeClass('top0');
    }, 1000);
}

// Minimize Additional Detail Div
function additionalDetailsMinimize() {
    $('.icExpand').removeClass('hide');
    $('.icMinimize').addClass('hide');
    $('.additionalDiv').slideUp(200);
}

// Validate Basic Info
function validateBasicInfo() {
  
        var getFirstNameValue = $('.firstname').val().length;
        var getLastNameValue = $('.lastname').val().length;
        var getPhoneValue = $('.phonenumber').val().length;
        var getPhone = $('.phonenumber').val();
        var getTitle = $('.title a.selected-text').attr('value');
        var getGender = $('.Gender a.selected-text').attr('value');
        var getEmailValue = $('.email').val().length;
        var getemail = $('.email').val();
        if(getFirstNameValue > 0 && getLastNameValue > 0 && getPhoneValue > 0 && getEmailValue > 0 && getTitle != 'All' && getGender != 'All')
        {   

            if($('.firstname').hasClass('hasError') || $('.lastname').hasClass('hasError'))
            {}
            //else if(validatePhone(getPhone) && isValidEmailAddress(getemail) ) { 
            else if(validatePhone(getPhone)) { 
                if($('.additional-details').is(':hidden'))
                {
                    $('#combobox').html('');
                    $('#combobox').html(window.comboboxList);
                    $(function() {
                        $("#combobox").combobox({
                            selected: function(event, ui) {
                                $('#log').text('selected ' + $("#combobox").val());
                            }
                        });
                        $("#combobox").next().next('.ui-combobox').remove();
                        
                    });
                    $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
                    $('.next-saveDiv').removeClass('hide');
                    $('.newLead').addClass('opened');
                    $('.btn-nextDetails').trigger('click');
                    $('.add-addressClick').trigger('click');
                    $('.initialScreen').removeClass('hideshow');
                }
                
            }
        }
}

// Show booking Error

function showBookingError() {
    $('.bookingError').removeClass('opacity0');
    setTimeout(function(){ 
        $('.bookingError').addClass('opacity0');
    }, 3000);
}

// Validating email function
function isValidNames(names) {
    //var pattern = new RegExp(/^[a-zA-Z\s]+$/);
    ///^[A-Za-z0-9-]+$/
    var pattern = new RegExp(/^[A-Za-z-]+$/);
    return pattern.test(names);
}

// Validating email function
function isValidEmailAddress(emailAddress) {
    var pattern = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
    return pattern.test(emailAddress);
}

// Validating Phone Number
function validatePhone(PhoneNumber) {
    var filter = /^[0-9  + ]+$/
    if (filter.test(PhoneNumber)) {
        return true;
    }
    else {
        return false;
    }
}
// Validating number Only
function validateNumber(Number) {
    //var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    var filter = /^[0-9 ]+$/
    if (filter.test(Number)) {
        return true;
    }
    else {
        return false;
    }
}

// Get Only Dates
function getOnlyDates() {
    
        var getOnlyDate = '';
        var getWeek = $('.weekSelection a').filter('.active').attr('value');
        if(getWeek == "one")
        { getOnlyDate = $('.daySelection a').filter('.active').attr('only-date1'); }
        else if(getWeek == "two")
        { getOnlyDate = $('.daySelection a').filter('.active').attr('only-date2'); }
        else
        { getOnlyDate = $('.daySelection a').filter('.active').attr('only-date3'); }

        return getOnlyDate;
}

function basicInfoUserDp() {
    var getSrc = $('ul.assignToDiv .dropdownOptions li').filter('.activeField').find('a img').attr('src');
    var setImg = "<img src='"+getSrc+"' class='d-i-b'/>";
    $('.selectedAgent span').slideDown(100);
    $('.userDp').html(setImg);
    $('.userName').html($('ul.assignToDiv .dropdownOptions li').filter('.activeField').find('a').attr('value'));
    $('.InspectionCheck').each(function () {
        this.checked = true;
    });
}      

function cancelBookedBookingAction() {
    $('.next-saveDiv').removeClass('hide');
    $('.bookNowDiv').addClass('hide');
    additionalDetailsExpand();
    $('.savedBooking').addClass('hide');
    $('.bookingViewIcon').removeClass('hide');
    $('.bookingHeading').addClass('hide');
}

function resetForm() {
        $('.additional-details').html(window.GetAdditionalDetails);
        $('.add-address').slideUp(50);
        $('.next-saveDiv').addClass('hide');
        $('.newLead').removeClass('opened');
        $('.additional-details').removeClass('opened');
        $('.additional-details').slideUp(0);
        $('.btn-nextDetails').removeClass('hide');
        $('.btn-saveDetails').addClass('hide');
        $('.btn-bookNow').addClass('hide');
        $('.basicInfo span').hide();
        $('.basicInfo input').val('');
}

function loadAgentCalendar(userId , $data) {
    
    var data = {
        lead_owner : userId,
        booking_date : $data
    };
       //Ajax Call Get Calender Data
       
   $.ajax({
            
          type: "GET",
          url: "dashboard/ajaxGetDataforCalender",
          data: data,
          success: function (data) {
           //Convert Json into Array
            
var obj = '';

                   
try{
                         
  obj = JSON.parse(data);
                   
}
                   
catch(e)
                   
{
                       
return false;                  
}               
           var plo = '';
           var arr = [];
           for(var x in obj)
           {
               arr.push(obj[x]);
           }

          for (i = 1; i < arr.length; i++) {
              
              if(i <= 9)
              {
                  //var con = '0'+i;
                  plo = {i:obj['Day0'+i]}

              }
              else
              {
                  plo = {i:obj['Day'+i]}

              }
           
            }
      }
      
  });
    
}


/*------------------------------------------------------------------*/
/*--------------------- --End Create New Lead --------------------- */
/*------------------------------------------------------------------*/

// Open Partner Popup

$(document).on('click','.addPartnerToLead', function (e) {
      
      $('.dialogeBox.addNewPartner').removeClass('hide');
});

$(document).on('click','.showaddPartnerToLead', function (e) {
      
      $('.dialogeBox.addNewPartner').removeClass('hide');
});


/* ----------------------------------------------------*/

// Save partner details

$(document).on('click','.savePartner', function (e) {
      partnerValidation();
      if(window.partnervalidState == false)
      {
        return false;
      }

      var model = getValuesFromPartnerForm();
      var setName = window.partnerFirstName + ' ' + window.partnerLastName;
      $('.showaddPartnerToLead').html(setName);
      $('.addPartnerToLead').addClass('hide');
      $('.showPartnerDetails').removeClass('hide');
      
      // $.ajax({

      //  type: "POST",
      //  url: "/dashboard/partner",
      //  data: model,
      //  success: function (data) {
      //  }
      // }); 


      $('.dialogeBox.addNewPartner').addClass('hide');
});

/* ----------------------------------------------------*/

// Cancel Partner

$(document).on('click','.cancelPartner', function (e) {
      
      $('.dialogeBox.addNewPartner').addClass('hide');
});

/* ----------------------------------------------------*/

// Same Address as Partner 

$(document).on('change','#addressAsPartner', function (e) {
      
      var el = $(this);
      var checked = el.is(':checked')
      alert(checked);
      if(checked)
      {
        var CountryName = $('.countryDiv .ui-combobox input.ui-autocomplete-input').val();
        var stateName = $('#stateDropdown').html();
        var addressName = $('#fullAddress').val();

        $('.partnerCountryDiv .ui-combobox input.ui-autocomplete-input').val(CountryName);
        $('.dropdown.partnerState a.selected-text').attr('value', stateName);
        $('#partnerStateDropdown').html(stateName);
        $('#partnerFullAddress').val(addressName);

        $('.partnerStateError').addClass('opacity0'); 
        

      }
});

/* ----------------------------------------------------*/

// Remove Error messages

 $(document).on('keyup', '.customerFields input', function () {
    var el = $(this);
      if($(this).val().length > 0)
      {
        el.next('label').addClass('opacity0');
      }    
    });// End

// Remove Error messages

/* ----------------------------------------------------*/

// Validating Partner firstName and Last Name

$(document).on('keyup', '.customerFields input#partnerFirstName, .customerFields input#partnerLastName', function () {
  
  $(this).closest('.relative').find('.partnerError').addClass('opacity0');
    var getName = $(this).val();
    if ($.trim(getName).length == 0) {
        $(this).next().next('label.partnerfirstError').addClass('opacity0');
        $(this).removeClass('hasError');
    }
    else if (isValidNames(getName)) {
        $(this).next().next('label.partnerfirstError').addClass('opacity0');
        $(this).removeClass('hasError');
        //validateBasicInfo();
    }
    else {
        $(this).next().next('label.partnerfirstError').removeClass('opacity0');
        $(this).addClass('hasError');
    }
});// End

/* ----------------------------------------------------*/


//Validating Phone Number For Partner

$(document).on('keyup', '.customerFields input#partnerPhone', function () {
     
    var getphone = $(this).val();
    var getphoneLength = $(this).val().length;
    $(this).next('label').addClass('opacity0');
    if(getphoneLength == 0)
    {
      $(this).next().next('label.partnerValidError').addClass('opacity0');
      $(this).removeClass('hasError');
    }
    else if(!validatePhone(getphone)) { 
        $(this).next().next('label.partnerValidError').removeClass('opacity0');
        $(this).addClass('hasError');
    }
    else
    {
        $(this).next().next('label.partnerValidError').addClass('opacity0');
        $(this).removeClass('hasError');
    }

});// End

  
/* ----------------------------------------------------*/

// Validating Email For Partner

$(document).on('keyup', '.customerFields input#partneremail', function () {
    $(this).closest('.relative').find('.partnerError').addClass('opacity0');
    
    var getValue = $(this).val().length;
    var getemail = $(this).val();
    if ($.trim(getemail).length == 0) {
        $(this).next('label').next('label.partnerRequiredError').addClass('opacity0');
        $(this).removeClass('hasError');
    }
    else if (isValidEmailAddress(getemail)) {
        $(this).next('label').next('label.partnerRequiredError').addClass('opacity0');
        $(this).removeClass('hasError');
        //validateBasicInfo();
    }
    else {
        $(this).next('label').next('label.partnerRequiredError').removeClass('opacity0');
        $(this).addClass('hasError');
    }

});// End



/* ----------------------------------------------------*/

// Getting Partner Values

function getValuesFromPartnerForm()
  {
      return {

          title : $('.dropdown.partnertitle a.selected-text').attr('value'),
          gender : $('.dropdown.partnerGender a.selected-text').attr('value'),
          getEmail : $('#partneremail').val(),
          firstname : $('#partnerFirstName').val(),
          lastname : $('#partnerLastName').val(),
          getPhone : $('#partnerPhone').val(),
          getCountry : $('#customerCountryName').attr('value'),
          getState : $('#partnerStateDropdown').closest('a.selected-text').attr('value'),
          getAddress : $('#partnerFullAddress').val()
      };
  }


/* ----------------------------------------------------*/

// Validation for partner

function partnerValidation()
    {

        debugger
        var title = $('.dropdown.partnertitle a.selected-text').attr('value');
        var gender = $('.dropdown.partnerGender a.selected-text').attr('value');
        var getEmail = $('#partneremail').val();
        var firstname = $('#partnerFirstName').val();
        var lastname = $('#partnerLastName').val();
        var getPhone = $('#partnerPhone').val();
        var getCountry = $('#customerCountryName').attr('value');
        var getState = $('#partnerStateDropdown').closest('a.selected-text').attr('value');
        var getAddress = $('#partnerFullAddress').val();
        
        var isNotFirstNameValid = $('#partnerFirstName').hasClass('hasError');
        var isNotLastNameValid = $('#partnerLastName').hasClass('hasError');
        var isNotPhoneValid = $('#partnerPhone').hasClass('hasError');
        var isNotEmailValid = $('#partneremail').hasClass('hasError');

        window.partnerFirstName = firstname;
        window.partnerLastName = lastname;

        if( isNotFirstNameValid == true || isNotLastNameValid == true  || isNotPhoneValid == true  || isNotEmailValid == true )
        {

          window.partnervalidState = false;
          return false;
        }

        var checkCountry = true;
        if(getCountry == 'Australia')
        {
            if(getState == 'All')
            {
              checkCountry = false
            }
            else
            {
              checkCountry = true
            }
        }
        
        if(title == 'All' || gender == 'All' || firstname == '' || lastname == '' || getPhone == '' || getEmail == '' || checkCountry == false)
        {
            // Title
            if(title == 'All')
            {$('.partnertitle').next('label.partnerError').removeClass('opacity0');}
            else
            {$('.partnertitle').next('label.partnerError').addClass('opacity0');}

            // Gender

            if(gender == 'All')
            {$('.partnerGender').next('label.partnerError').removeClass('opacity0');}
            else
            {$('.partnerGender').next('label.partnerError').addClass('opacity0');}

            // FirstName

            if(firstname == '')
            {$('#partnerFirstName').next('label.partnerError').removeClass('opacity0');}
            else
            {$('#partnerFirstName').next('label.partnerError').addClass('opacity0');}

            // Last Name

            if(lastname == '')
            {$('#partnerLastName').next('label.partnerError').removeClass('opacity0');}
            else
            {$('#partnerLastName').next('label.partnerError').addClass('opacity0');}

            // Phone

            if(getPhone == '')
            {$('#partnerPhone').next('label.partnerError').removeClass('opacity0');}
            else
            {$('#partnerPhone').next('label.partnerError').addClass('opacity0');}

            // Email

            if(getEmail == '')
            { 
              {$('#partneremail').next('label.partnerError').removeClass('opacity0');}
            }
            else if(!isValidEmailAddress(getEmail))
            {
              $('.emailexists').addClass('opacity0');
              $('#email').next('label').next('label').removeClass('opacity0');
            }

            // Country State

            if(checkCountry == false)
            { 
                $('.partnerStateError').removeClass('opacity0'); 
            }
            else
            {$('.partnerStateError').addClass('opacity0'); }
            
            window.partnervalidState = false;
            return false;
        }
        else
        { 
            window.partnervalidState = true;
        }

    }


/* ----------------------------------------------------*/

function onFocusOutspartnerPhone()
{

}




/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/




/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/



/*------------------------------------------------------------------*/
/*---------------------- Start Dashboard Code --------------------- */
/*------------------------------------------------------------------*/

//Dashboard Submit Button

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// header tabs click
$(document).on('click','.middleTabs a', function (e) {
    $(".newactions a").removeClass('active');
    $(".middleTabs a").removeClass('active');
    $(this).addClass('active');
    if($(this).find('i').hasClass('icon-leads'))
    {
        $('.newactions a').addClass('hide');
        $('.new-Lead').removeClass('hide');
        $('.dateSelected').removeClass('hide');
        //$('.newLead').addClass('hide');

        $('.newLead').addClass('maxHeightHide');

        $('.newLeaveContainer').hide();
        $('.dashboardContainer').addClass('hide');
        $('.leavesContainer').addClass('hide');
        $('.leadsContainer').removeClass('hide');
        $('.leadDeailContainer').addClass('hide');

    }
    else if($(this).find('i').hasClass('icon-leave'))
    {
        $('.newactions a').addClass('hide');
        $('.new-Leave').removeClass('hide');
        $('.dateSelected').addClass('hide');
        //$('.newLead').addClass('hide');

        $('.newLead').addClass('maxHeightHide');


        $('.dashboardContainer').addClass('hide');
        $('.leadsContainer').addClass('hide');
        $('.newLeaveContainer').hide();
        $('.leavesContainer').removeClass('hide');
        $('.leadDeailContainer').addClass('hide');
    }
    else
    { 
        $('.newactions a').addClass('hide');
        $('.dateSelected').addClass('hide'); 
        //$('.newLead').addClass('hide');

        $('.newLead').addClass('maxHeightHide');

        $('.leadsContainer').addClass('hide');
        $('.newLeaveContainer').hide();
        $('.leavesContainer').addClass('hide');
        $('.dashboardContainer').removeClass('hide');
        $('.leadDeailContainer').addClass('hide');
    }
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.nextInlinePerson', function (e) {
  $(".newactions a").removeClass('active');
  $(".newactions a.new-Lead").trigger('click');
  $('.newactions a.new-Lead').addClass('active');
});


//create new lead and leave

$(document).on('click','.newactions a', function (e) {

    $(".newactions a").removeClass('active');
    $(this).addClass('active');

    if($(this).hasClass('new-Lead'))
    {   
        $('.triggerCalendar').trigger('click');
        $('.searchArea').removeClass('hide');
        $('.newLeaveContainer').hide();
        $('.leadsContainer').addClass('hide');
        $('.leavesContainer').addClass('hide');
        $('.dashboardContainer').addClass('hide');
        $('.newLead').removeClass('maxHeightHide');
        $('.dashboard-header').addClass('hide');
        $('.leadDeailContainer').addClass('hide');
        $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
        $('.newLead').removeClass('inEditMode');
        $(".hideOnSavenBook").removeClass('hide'); 
        $(".calendarShowOnBook").addClass('hide');
        endCalendarLoading();

    }
    else
    {
        $('.leadsContainer').addClass('hide');
        $('.leavesContainer').addClass('hide');
        $('.dashboardContainer').addClass('hide');

        //$('.newLead').addClass('hide');
        $('.newLead').addClass('maxHeightHide');
        $('.dashboard-header').addClass('hide');
        $('.newLeaveContainer').slideDown(300);
        $('.leadDeailContainer').addClass('hide');
        window.newLeaveHtml = $('.newLeaveContainer').html();
    }

});

function endCalendarLoading()
    {
        
        setTimeout(function(){ 
          $('.NewCalendarContainer').find('.loading').remove();
        }, 500);
    }
  function endCalendarLoading2()
  {
      
      setTimeout(function(){ 
        $('.NewCalendarContainer').find('.loading').remove();
      }, 1500);
  }
    
/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Cancel Add New Leave

$(document).on('click','.btn-cancelLeave', function (e) {

        $('.calendarLeave ').addClass('maxHeightHide');
        $('.leavesContainer').addClass('hide');
        $('.dashboardContainer').addClass('hide');
        // New Leade New Leave Start
        $('.newactions a, .middleTabs a').removeClass('active');
        $('.new-Leave').addClass('hide');
        $('.new-Lead').removeClass('hide');
        // New Leade New Leave End
        $('.leadTab').addClass('active');
        $('.newLead').addClass('maxHeightHide');
        $('.dashboard-header').removeClass('hide');
        $('.newLeaveContainer').hide();
        $('.leadsContainer').removeClass('hide');
        $('.calendarLeave').addClass('maxHeightHide');
        //Reset New Leave Page
        $('.newLeaveContainer').html(window.newLeaveHtml);

        var getTodayDate = moment(); //Get the current date
        getTodayDate.format("YYYY-MM-DD"); 
          $('input[name="daterange"]').daterangepicker({
              minDate:getTodayDate,
              startDate: getTodayDate, 
              locale: {
                  format: 'YYYY-MM-DD'
              }
          }, function (start, end, label) {
        });
        var getTodaysDate = moment(getTodayDate).add(0, 'M').format('YYYY-MM-DD');
        $('input[name="daterange"]').val(getTodaysDate).attr('startdate',getTodaysDate);

});

//Function GetValues From the SaveLeads

    function getValuesFromLeaveForm()
    {
        
        return {
          
            startDate : $("#dateRange").attr('startDate'),
            endDate : $("#dateRange").attr('endDate'),
            AssignUs : $("#assign_us_Dropdown2").text(),
            Reason : $("#Reasons").text(),
            Id : $(".assignToDivLeave a.selected-text").attr('asigneeId-value'), 
            
        };
        
    }

// Save Add New Leave 

$(document).on('click','.btn-saveDetailsLeave', function (e) {
       
        $('.calendarLeave ').addClass('maxHeightHide');
        var leaveDate = $('#dateRange').val();
        var leaveAgent = $('.leaveAgent').attr('value');
        var leaveReason = $('.leaveReason').attr('value');

        if(leaveDate == "" || leaveAgent  == 'All' ||  leaveReason == 'All')
        {
          
          if(leaveDate == "")
          { 
            $('#dateRange').next('.showError').removeClass('opacity0');
          }
          else
          { 
            $('#dateRange').next('.showError').addClass('opacity0');
          }
          if(leaveAgent == 'All')
          {  
            $('.assignToDivLeave').next('.showError').removeClass('opacity0');
          }
          else
          { 
            $('.assignToDivLeave').next('.showError').addClass('opacity0');
          }
          if(leaveReason == 'All')
          {  
            $('.Reason').next('.showError').removeClass('opacity0');
          }
          else
          { 
            $('.Reason').next('.showError').addClass('opacity0');
          }
          return false;
        }
        
        //AjaxCallSaveLeadsOnSaveClick--Start
         
         var model = getValuesFromLeaveForm();
         if(model.endDate == null)
         {
          model.endDate = model.startDate;
         }

         $.ajax({

            type: "POST",
            url: "/leave/ajaxSaveLeaves",
            data: model,
            success: function (data) {
              //AjaxCallSaveLeadsOnSaveClick--End
        
                loadLeads();

                //Setting header changes
                
                
                $('.dashboardContainer').addClass('hide');
                // New Leade New Leave Start
                $('.newactions a, .middleTabs a').removeClass('active');
                $('.new-Lead').addClass('hide');
                $('.new-Leave').removeClass('hide');
                // New Leade New Leave End
                $('.leaveTab').addClass('active');
                $('.newLead').addClass('maxHeightHide');
                $('.dashboard-header').removeClass('hide');
                $('.newLeaveContainer').hide();
                $('.leadsContainer').addClass('hide');
                $('.leavesContainer').removeClass('hide');
                $('.calendarLeave').addClass('maxHeightHide');
                //Reset New Leave Page
                $('.newLeaveContainer').html(window.newLeaveHtml);
                loadAddNewLeaveCalendar();
                SetLeaveContent(window.startingMonth,window.startingYear);
                loadLeaveCalendar();
            }
          });    
        
        
})

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/


// Month Year selection dropdown

$(document).on('click','.leaveMonth', function (e) {
    $(".monthDropdown").slideToggle('150');
    $(".yearDropdown").slideUp(10);
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.leaveYear', function (e) {
    $(".yearDropdown").slideToggle('150');
    $(".monthDropdown").slideUp(10);
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.monthDropdown span', function (e) {
    var getValue = $(this).attr('value');
    $('.leaveMonth span').html(getValue);
    $(".monthDropdown").slideUp(10);
    $('.leaveMonth span').attr('month', $(this).attr('month'));
    // Get month and year
    var getMonth = $('.leaveMonth span').attr('month');
    var getYear = $('.leaveYear span').attr('year');

    var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    var getDayName = new Date(getMonth+'/1/'+getYear);
    var getDay = weekday[getDayName.getDay()];
    
    SetLeaveContent(getMonth,getYear);
});


/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.yearDropdown span', function (e) {
    var getValue = $(this).attr('value');
    $('.leaveYear span').html(getValue);
    $(".yearDropdown").slideUp(10);
    $('.leaveYear span').attr('year',$(this).attr('year'));
    var getMonth = $('.leaveMonth span').attr('month');
    var getYear = $('.leaveYear span').attr('year');

    var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    var getDayName = new Date(getMonth+'/1/'+getYear);
    var getDay = weekday[getDayName.getDay()];
    
    SetLeaveContent(getMonth,getYear);

});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Setting leave records

setTimeout(function(){ 
    window.leavesContentList = $('.leaveContent').html();
}, 500);


function SetLeaveClass(ReasonForLeave) {

  var leaveClass = "gray";
  //if(ReasonForLeave == "Sick leave")
  //{ leaveClass = "darkgreen"; }
  //else if(ReasonForLeave == "Other")
  //{ leaveClass = "lightgreen"; }
  //else
  //{ leaveClass = "orange"; }
  
  return leaveClass;
}

function SetLeaveContent(getMonth,getYear) {
    $('.all-Leaves-Content .loading').show();
  
    // Get number of days in month
    /* -------------------------*/
    var y = getYear;
    var m = getMonth;
    var totalDays = new Date(y,m,1,-1).getDate();
    totalDays++;
    /* -------------------------*/

    var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    
    var getMonth = getMonth;
    var getYear = getYear;

    setTimeout(function(){ 
        $('.leaveYear span').html(getYear);
        $('.leaveYear span').attr('year',getYear); 
    }, 1000);
    var getDayName = new Date(getMonth+'/1/'+getYear);
    var getDay = weekday[getDayName.getDay()]; 
    
    // setting leave record

    var i=0;

    if(getDay == "Sunday")
    { i=0 }
    else if(getDay == "Monday")
    { i=1 }
    else if(getDay == "Tuesday")
    { i=2 }
    else if(getDay == "Wednesday")
    { i=3 }
    else if(getDay == "Thursday")
    { i=4 }
    else if(getDay == "Friday")
    { i=5 }
    else
    { i=6 }
    
    var j = 1;
    
    // Empty Divs 
    $('.dateArea').html('');
    $('.agentLeaves').html('');

    // Getting Date

      $.ajax({
          type: "GET",
          url: "/leave/ajaxGetAllLeaves",
          data: {month : getMonth , year : getYear},
          success: function (data) {

            var leavesData= '';               
            try{
              leavesData= JSON.parse(data);
            }           
            catch(e)
            {
              $('.all-Leaves-Content .loading').hide(100);             
              return false;                  
            }

              setTimeout(function(){
                $('.leaveContent').html(window.leavesContentList);
                var counter = 0;
                $('.leavesDetail').each(function(){
                  var setHtml = "";
                  var getIndex = $(this).attr('index');
                  if(getIndex == i)
                    {
                      if( j >= totalDays)
                      {
                          $(this).remove();
                          i++;
                          j++;
                          counter++;
                      }
                      else
                      {
                          $(this).find('.dateArea').html(j);
                          var getsubLength = leavesData[counter].length;
                          var getTotalListCount = leavesData[counter].length;
                          getTotalListCount--;
                          for (var k = 0; k < getsubLength; k++) {
                            var innerResult = leavesData[counter];
                            if(innerResult[0].Leave_AssignUserName != undefined)
                            {
                                  var ReasonForLeave = leavesData[counter][k].Leave_Reason;
                                  /*---------------------------------------------------*/
                                  // Reason for Leave
                                  var reasonSick = leavesData[counter][k].Leave_Reason;
                                  if(leavesData[counter][k].Leave_Reason == "Sick leave")
                                  {
                                    reasonSick = "Sick all day";
                                  }
                                  /*---------------------------------------------------*/
                                  // Leave class check

                                  var leaveClass = SetLeaveClass(ReasonForLeave);
                                  
                                  /*---------------------------------------------------*/
                                  // Set Leave content here

                                  if(k == 2)
                                  { 
                                    var remainingCount = getTotalListCount - k + 1;
                                    if(remainingCount == 1)
                                    { setHtml += "<div class='absenseTooltip'>"+remainingCount+" more absence</div>"; }
                                    else
                                    { setHtml += "<div class='absenseTooltip'>"+remainingCount+" more absences</div>"; }

                                    setHtml += "<div class='tooltipContainer' hidden='hidden'><img src='/images/tooltipArrow.png' /><div class='full tooltipHeight'>";
                                  }
                                  if(k > 1)
                                  {
                                    setHtml += "<div class='details "+leaveClass+"'><span>"+leavesData[counter][k].Leave_AssignUserName+"</span><span>"+reasonSick+"</span></div>";
                                  }
                                  else
                                  {
                                    setHtml += "<div class='details "+leaveClass+"'><span>"+leavesData[counter][k].Leave_AssignUserName+"</span><span>"+reasonSick+"</span></div>";
                                  }
                                  if(k==getTotalListCount)
                                  { setHtml += "</div></div>"; }
                                  
                            }
                            else
                            {
                                // No Leave Here
                                $(this).find('.dateArea').html(j);
                            }
                          }
                          i++;
                          j++;
                          counter++;
                          $(this).find('.agentLeaves').html(setHtml);
                      }
                    }
                });
                    
                
                 setTimeout(function(){ 
                    $('.all-Leaves-Content .loading').hide(100);
                }, 500);
            }, 1000);
          }
      });
}

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/



// tooltip show hide

$(document).on('click','.absenseTooltip', function (e) {
    if($(this).next('.tooltipContainer').is(':hidden'))
    {
        $('.tooltipContainer').hide();
        $(this).next('.tooltipContainer').slideDown(100);
    }
    else
    {
        $('.tooltipContainer').slideUp(50);
    }
});


/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/



$(document).on('click', function(event){
    var container = $(".agentLeaves");
    if (!container.is(event.target) &&            
        container.has(event.target).length === 0)
        {
            $('.tooltipContainer').slideUp(50);
        }
});// End

$(document).on('click', function(event){
    var container = $(".dialoageOverlay");
    if (!container.is(event.target) &&            
        container.has(event.target).length === 0)
        {
            
        }
});// End

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/



// Show main loading
function showMainLoading(){
    $('.mainLoader').show();
    setTimeout(function(){ 
        $('.mainLoader').fadeOut(300);
    }, 2500);
}

// Show main loading
function showMainCalendarLoading(){
    $('.mainLoader').show();
    setTimeout(function(){ 
        $('.mainLoader').fadeOut(300);
    }, 1000);
}

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Reloading Calendar
function SetCalendar(calendarDate){

    $('#multiCalendar').multiDatesPicker('resetDates');
    $('#multiCalendar').multiDatesPicker('destroy');
    $('#multiCalendar').multiDatesPicker({
        defaultDate : calendarDate,
        altField: '#altField',
        onSelect:function(){
        
      }
    });
}

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

function showCalendarError(){
    $('.calendarfields').removeClass('opacity0');
    setTimeout(function(){ 
        $('.calendarfields').addClass('opacity0');
    }, 3000);
}

// Month Year selection dropdown for calendar
/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.calendarLeaveMonth', function (e) {
    $(this).closest('.calendarContainer').find(".monthCalendarDropdown").slideToggle(150);
    $(this).closest('.calendarContainer').find(".yearCalendarDropdown").slideUp(10);
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.calendarLeaveYear', function (e) {

    $(this).closest('.calendarContainer').find(".yearCalendarDropdown").slideToggle(150);
    $(this).closest('.calendarContainer').find(".monthCalendarDropdown").slideUp(10);

});

// calendar month selection
/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// calendar suggestedDate
/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.suggestedDate', function (e) {

    $('#suggestedDateCalender').multiDatesPicker({
        minDate: 0,
        onSelect:function(data, event){
          
          $('#suggestedDateCalender').multiDatesPicker('resetDates');
          $('#suggestedDateCalender').multiDatesPicker('destroy');
          var el = $(this);
          var selectedDate = data;
          var SelectedDay = event.selectedDay;
          if(SelectedDay < 10)
            SelectedDay = '0'+SelectedDay;

          var SelectedMonth = event.selectedMonth;

          var SelectedMonthIncrement = SelectedMonth;
          SelectedMonthIncrement++;
          if(SelectedMonthIncrement < 10)
            SelectedMonthIncrement = '0'+SelectedMonthIncrement;

          var SelectedYear = event.selectedYear;
          var setCompleteDate = SelectedYear + '-' + SelectedMonthIncrement + '-' + SelectedDay;
          //2018-02-12

          // Get Day Name
          var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
          var getDayName = new Date(data);
          var getDay = weekday[getDayName.getDay()];

          // Get Month Name

          var m_names = ['January', 'February', 'March', 
               'April', 'May', 'June', 'July', 
               'August', 'September', 'October', 'November', 'December'];

          var getMonth = m_names[SelectedMonth];

          var setDate = getDay + ' ' + SelectedDay + ' ' + getMonth ;
          //Saturday 10 March 8:00 - 9:00 AM

          $('.suggestedDate, #bookingDate').html(setDate);
          $('#bookingDate').attr('time', '');
          $('#bookingDate').attr('timezone', 'AM');
          $('#bookingDate').attr('date', setCompleteDate);
          $('#bookingDate').addClass('nowCanSave');
          
        }

    });

});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Suggested Date on outside click 

    $(document).on('click' ,function(event){
      var container = $(".suggestedDateCalender, .suggestedDate");
        if (!container.is(event.target) && container.has(event.target).length === 0)
        {
          $('#suggestedDateCalender').multiDatesPicker('resetDates');
          $('#suggestedDateCalender').multiDatesPicker('destroy');
        }
    });// End

// Suggested Date on outside click 


/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/


$(document).on('click','.monthCalendarDropdown span', function (e) {

    
    var getValue = $(this).attr('value');
    //$('.leadListCalendar .calendarLeaveMonth span').html(getValue);
    //$(".leadListCalendar .monthCalendarDropdown").slideUp(10);
    //$('.leadListCalendar .calendarLeaveMonth span').attr('month', $(this).attr('month'));
    var el = $(this);
    $(this).closest('.calendarContainer').find(".calendarLeaveMonth span").html(getValue);
    $(this).closest('.calendarContainer').find(".monthCalendarDropdown").slideUp(10);
    $(this).closest('.calendarContainer').find(".calendarLeaveMonth span").attr('month', $(this).attr('month'));

    // Get month and year
    var getMonth = $(this).closest('.calendarContainer').find(".calendarLeaveMonth span").attr('month');
    var getYear = $(this).closest('.calendarContainer').find(".calendarLeaveYear span").attr('year');
    var calendarDate = getMonth+'/1/'+getYear;
    if($(this).closest('.calendarContainer').hasClass('CloseleadListCalendar'))
    {
      el.closest('.closeLeadPopup').find('.closeLeadDate').val("");
      el.closest('.closeLeadPopup').find('.closeLeadDate').removeAttr('value');
      SetCalendarCloseLead(calendarDate);
    }
    else
    {
      SetCalendar(calendarDate);
    }
    
});

// calendar year selection
/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.yearCalendarDropdown span', function (e) {
    var el = $(this);
    var getValue = $(this).attr('value');
    $(this).closest('.calendarContainer').find(".calendarLeaveYear span").html(getValue);
    $(this).closest('.calendarContainer').find(".yearCalendarDropdown").slideUp(10);
    $(this).closest('.calendarContainer').find(".calendarLeaveYear span").attr('year',$(this).attr('year'));
    // Get month and year
    var getMonth = $(this).closest('.calendarContainer').find(".calendarLeaveMonth span").attr('month');
    var getYear = $(this).closest('.calendarContainer').find(".calendarLeaveYear span").attr('year');
    var calendarDate = getMonth+'/1/'+getYear;
    if($(this).closest('.calendarContainer').hasClass('CloseleadListCalendar'))
    {
      el.closest('.closeLeadPopup').find('.closeLeadDate').val("");
      el.closest('.closeLeadPopup').find('.closeLeadDate').removeAttr('value');
      SetCalendarCloseLead(calendarDate);
    }
    else
    {
      SetCalendar(calendarDate);
    }

});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// calendar short cut option selection

$(document).on('click','.calenderfilterOptions span', function (e) {
  
    $(this).closest('.dropdownOptions').slideUp(50);
    $('.LeadsCalendar span').html($(this).attr('value'));
    $('.LeadsCalendar').attr('value', $(this).attr('value'));
    $('.LeadsCalendar i').removeClass('icon-downarrow').addClass('icon-close');
    $('.calendarDropdown').slideUp(0);
    
    if($(this).attr('value') == "Today")
    { 
        $('.dateSelected').html(window.todayDate);
    }
    else if($(this).attr('value') == "Yesterday")
    {
        $('.dateSelected').html(window.todayDate2);
    }
    else if($(this).attr('value') == "This week")
    {
        $('.dateSelected').html(window.weeklyDate);
    }
    else if($(this).attr('value') == "This month")
    {
        $('.dateSelected').html(window.thisMonth);
    }
    else if($(this).attr('value') == "This year")
    {
        var YearDate = 'January 1' + '<span class="subTopText">st</span>' + ' - December 31' + '<span class="subTopText">st</span>';
        //$('.dateSelected').html('January 1st - December 31st');
        $('.dateSelected').html(YearDate);
    }

    loadLeads();
});



/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Loop up Code

$(document).on('click','.lookup', function (e) {
    $('.showLookups').removeClass('hide').slideDown();
    $('.customerList .ui-autocomplete-input').val('');
    $('.customerList .ui-combobox input').attr('placeholder','Type to search');
});

$(document).on('click','.customerHeading span', function (e) {
  $('.showLookups').slideUp();
  setTimeout(function(){ $('.showLookups').addClass('hide'); }, 100);
});


/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Rings filter

$(document).on('click','.rings a', function (e) {
    $('.rings a').removeClass('active');
    $(this).addClass('active');
    loadLeads();
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Get calendar Dates

$(document).on('click','.submitDates', function (e) {
    var getDates = $('#altField').val();

    if(getDates == "")
    {
        showCalendarError();
        $('.calendarDropdown').slideUp(0);
        return false
    }   
    $('.LeadsCalendar i').removeClass('icon-downarrow').addClass('icon-close');
    $('.LeadsCalendar').attr('value',getDates);
    $('.LeadsCalendar span').html('Dates');
    $(this).closest('.dropdownOptions').slideUp(50);
    $('#multiCalendar').multiDatesPicker('resetDates');
    $('.calendarDropdown').slideUp(0);
    loadLeads();
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.icon-close', function (e) {
    var el = $(this);
    el.removeClass('icon-close').addClass('icon-downarrow');
    el.closest('.selected-text').attr('value','All');
    if(el.closest('a.selected-text').hasClass('agentStatus'))
    {
        el.prev('span').html('Status');
        loadLeads();
    }
    if(el.closest('a.selected-text').hasClass('Referral'))
    {
        el.prev('span').html('Referral');
        loadLeads();
    }
    else if(el.closest('a.selected-text').hasClass('LeadsCalendar'))
    {
        $('.LeadsCalendar span').html('This month');
        $('.dateSelected').html(window.thisMonth);
        loadLeads();
    }
    return false
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

// Loads Leads


function loadLeads(){
    $('.loadLeadsHere').hide();
    $('.leadsContainer .loading').fadeIn(400);
    setTimeout(function(){ 
        
        var agentBudget = $('.rings a').filter('.active').attr('value');
        if (agentBudget == undefined || agentBudget =="") { agentBudget = "all"}
        var agentStatus = $('.agentStatus').attr('value');
        if (agentStatus == undefined || agentStatus =="") { agentStatus = "All"}
        var agentReferral = $('.Referral').attr('value');
        if (agentReferral == undefined || agentReferral =="") { agentReferral = "All"}
        var agentDate = $('.LeadsCalendar').attr('value');
        if (agentDate == undefined || agentDate =="") { agentDate = "All"}
        var data = {
          
          budget : agentBudget,
          lead_status : agentStatus,
          referral : agentReferral,
          booking_date : agentDate
          
        };
       
        // Starting ajax call
        $.ajax({
        
                type: "GET",
                url: "/dashboard/ajaxGetLeadsByBudget",
                data: data,
                success: function (data) {   

                    // convert json into Array
                  
                  var parsed = '';
                  try{         
                    parsed = JSON.parse(data);              
                  }
                    catch(e)
                  {             
                    $('.leadsContainer .loading').hide();
                    return false;
                  }
                
                    var leads = [];
                    for(var x in parsed){
                      leads.push(parsed[x]);
                    }

                    leads.sort(function(a, b) {
                        var textA = a.idOfUser;
                        var textB = b.idOfUser;
                        return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                    });

                    // Get Smaller lead count number
                
                    var makeList = [];
                    var getLeadsCount = leads.length;
                    getLeadsCount--;

                    for(i=0; i < leads.length; i++)
                    {
                        makeList.push(leads[i].count);
                    }
                    //$.each(leads[getLeadsCount].items, function(key, value){

                    //  makeList.push(value.count);
                      //count++
                    //});
                    var getSmallestNumber =  Math.min.apply(null, makeList);
                    var repititionCheck = 1000000000000;
                    var repititionCheckAdded = 1000000000000;

                    var getNextInlineName = '';
                    //var setnextInLine = 0;
                    //$.each(leads[getLeadsCount].items, function(key, value){
                    //  var checkCount = value.count;

                    //  if(checkCount == getSmallestNumber)
                    //  {
                    //    if(setnextInLine == 0)
                    //    {
                    //      getNextInlineName = key;
                    //      setnextInLine++
                    //    }
                        
                    //  } 
                      //count++
                    //});
                    // Bind Array into Html

                    var setHtml = '';
                    
                    // Check for which user has less leads
                    
                    var getLeadsLength = [];
                   
                    var k = 1;
                    var l = 1;
                    var a = 0;
                    var nextInLineCounter = 0;
                    for (var i = 0; i < leads.length; i++) {

                        a++
                        // Setting Outer Container
                        setHtml += '<div class="one-fourth one-half-pad-left one-half-pad-right"><div class="full">';

                        // Setting Agent Image and Name

                        var getUserimage = leads[i].agentImage;
                        if(getUserimage == "empty")
                            getUserimage = 'sampleUser.png';

                        if(getSmallestNumber == leads[i].count && k == 1)
                        {
                            setHtml += '<div class="full activelead">';
                            k++;
                        }
                        else
                        {
                            setHtml += '<div class="full">';
                        }
                        var SalesRepName
                        setHtml += '<div class="agentImg-container"><img alt="Profile image" class="ProfileImg" src="/profile_image/'+ getUserimage  +'" /></div>';
                        setHtml += '<label class="robotolight fs-18  full one-half-gap-top">'+ leads[i].idOfUser  +'</label>';
                        setHtml += '</div>';
                        
                        // Setting Status Counts
                        var statusOpen = 0;
                        var statusClosed = 0;
                        var statusDealClosed = 0;
                        var statusAll = 0;
                        if(leads[i].count != 0)
                        {
                          for (var j = 0; j < leads[i].items.length; j++) {

                              statusAll++
                              var checkStatus = leads[i].items[j].LeadStatus
                              if(leads[i].items[j].LeadStatus == "Closed Lost")
                                  {
                                     statusClosed++
                                  }
                                  else if(leads[i].items[j].LeadStatus == "Deal closed")
                                  {
                                      statusDealClosed++
                                  }
                                  else
                                  {
                                      statusOpen++
                                  }

                          }
                        }
                        setHtml += '<div class="full fs-14 robotomedium half-pad-top">';
                        setHtml += '<label class="display-inline-block border-count-white">'+statusOpen+'</label>';
                        setHtml += '<label class="display-inline-block border-count-green hide">'+statusDealClosed+'</label>';
                        setHtml += '<label class="display-inline-block border-count-red">'+statusClosed+'</label>';
                        setHtml += '<label class="display-inline-block border-count-blue">'+statusAll+'</label>';
                        setHtml += '</div>';
                        
                        // Setting Agent Leads

                        if(leads[i].count != 0)
                        {

                        setHtml += '<div class="full triple-pad-left triple-pad-right triple-pad-top triple-pad-bottom "><ul class="lead-list full lh-38">';
                      
                        // Adding next inline lead

                          // Loop for leads
                          var status = '';
                          var referral = '';

                          // Incase for Normal Lead flow
                          for (var j = 0; j < leads[i].items.length; j++) 
                          {

                              // Setting Status
                              //var newArray = leads[j].items;
                              if(leads[i].items[j].LeadStatus == "Closed Lost")
                              { status = 'bg-red1'; }
                              else if(leads[i].items[j].LeadStatus == "Deal closed")
                              { status = 'bg-green1'; }
                              else
                              { status = 'bg-white'; }
                              // Setting refferal
                              if(leads[i].items[j].how_heard_title == "Google")
                              {  referral = '/images/ic-google.png'  }
                              else if(leads[i].items[j].how_heard_title == "Word of mouth")
                              {  referral = '/images/ic_wordMouth.png'  }
                              else if(leads[i].items[j].how_heard_title == "Previous client")
                              {  referral = '/images/ic_pClient.png'  }
                              else if(leads[i].items[j].how_heard_title == "Walk In")
                              {  referral = '/images/ic_walkIn.png'  }
                              else if(leads[i].items[j].how_heard_title == "Facebook")
                              {  referral = '/images/ic_facebook.png'  }
                              else if(leads[i].items[j].how_heard_title == "Instagram")
                              {  referral = '/images/ic_insta.png'  }
                              else
                              {  referral = '/images/ic_other.png'  }
                              // Binding Set Html Leads

                              if(getSmallestNumber == leads[i].count && nextInLineCounter == 0)
                              {
                                  setHtml += '<li class="relative nextInlinePerson"><div class="ellipsis color-green">Next In Line</div> <img class="referralImage" alt="" src="/images/ic_addplus.png" /></li>';
                                  nextInLineCounter++
                              }

                              if(leads[i].items[j].LeadStatus == "Open" || leads[i].items[j].LeadStatus == "To Opportunity")
                              {
                                 var getLeadPopup = $('#closeLead').html();
                                 setHtml += '<li class="relative userLeadId '+status+'"  userleadId="'+leads[i].items[j].Lead_id+'"><p class="absolute closeLeadClick">Close</p><div style="display:none" class="closeLeadPopup absolute full" leadId="'+ leads[i].items[j].Lead_id+'"><span class="closeLeadError opacity0 transition-ease-05 color-red">Please fill all fields</span>'+getLeadPopup+'</div><div class="leadUserName ellipsis">'+leads[i].items[j].LeadFirst_name+ ' ' +leads[i].items[j].LeadLast_name + '</div> <img class="referralImage" alt="Profile image" src="'+referral+'" /></li>';
                              }
                              else
                              {
                                 var getLeadPopup = $('#closeLead').html();
                                 setHtml += '<li class="relative userLeadId '+status+'"  userleadId="'+leads[i].items[j].Lead_id+'"><p class="absolute closeLeadClick">Open</p><div style="display:none" class="closeLeadPopup absolute full" leadId="'+ leads[i].items[j].Lead_id+'"><span class="closeLeadError opacity0 transition-ease-05 color-red">Please fill all fields</span>'+getLeadPopup+'</div><div class="leadUserName ellipsis">'+leads[i].items[j].LeadFirst_name+ ' ' +leads[i].items[j].LeadLast_name + '</div> <img class="referralImage" alt="Profile image" src="'+referral+'" /></li>';                                  
                              }
                          }
                          
                            

                          setHtml += '</ul></div>';

                        }
                        else
                        {
                          if(nextInLineCounter == 0)
                          {
                            setHtml += '<div class="full triple-pad-left triple-pad-right triple-pad-top triple-pad-bottom "><ul class="lead-list full lh-38">';
                        
                            setHtml += '<li class="relative nextInlinePerson"><div class="ellipsis color-green">Next In Line</div> <img class="referralImage" alt="" src="/images/ic_addplus.png" /></li>';
                                  
                            setHtml += '</ul></div>';
                          }
                          nextInLineCounter++

                        }

                        //Ending Outer Container
                        setHtml += '</div></div>';
                    }
                    
                    var getTotalWidth = a*400+10+'px';


                    setTimeout(function(){ 
                        
                        if(leads.length == 0)
                        {
                            $('.loadLeadsHere').html("<div class='norecord'><span>No Leads Found<span></div>");
                        }
                        else if(leads.length < 5)
                        {
                            $('.loadLeadsHere').html("<div class='insidescroll'>" + setHtml + "</div>");
                        }
                        else
                        {
                            $('.loadLeadsHere').html("<div class='insidescroll' style='width:"+getTotalWidth+"'>" + setHtml + "</div>");
                        }
                        
                        $('.leadsContainer .loading').hide();

                        // Apply horiztontal slider
                        // $('.loadLeadsHere').jScrollPane();

                        $('.loadLeadsHere').fadeIn(400);

                    }, 500);


            } // End Success Response
            
        }); // End Ajax Call

    }, 500);

} // End Function


$(window).load(function(){
    setTimeout(function(){ 
      loadLeads();   
  }, 1000);
});

/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/


/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/


/*------------------------------------------------------------------*/
/*----------------------- End  Dashboard Code --------------------- */
/*------------------------------------------------------------------*/


/*------------------------------------------------------------------*/
/*----------------------- Start Leave Code --------------------- */
/*------------------------------------------------------------------*/


function loadLeaveCalendar(){
    $(function () {
      var getTodayDate = moment(); //Get the current date
      getTodayDate.format("YYYY-MM-DD"); 
      $('input[name="daterange"]').daterangepicker({
          minDate:getTodayDate,
          startDate: getTodayDate, 
          locale: {
              format: 'YYYY-MM-DD'
          }
      }, function (start, end, label) {
      });
      var getTodaysDate = moment(getTodayDate).add(0, 'M').format('DD-MM-YYYY');
      $('input[name="daterange"]').val(getTodaysDate).attr('startdate',getTodaysDate);
      $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
          var startDate = picker.startDate;
          var endDate = picker.endDate;  
          var checkStartDate = startDate.format('YYYY-MM-DD');
          var checkEndDate = endDate.format('YYYY-MM-DD');
          if(checkStartDate === checkEndDate)
          {
            $('#dateRange').val(startDate.format('DD-MM-YYYY'));
            $('#dateRange').attr('startDate', startDate.format('YYYY-MM-DD'));
            $('#dateRange').attr('endDate', endDate.format('YYYY-MM-DD'));
          }
          else
          {
            var setDate = startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY');
            $('#dateRange').attr('startDate', startDate.format('YYYY-MM-DD'));
            $('#dateRange').attr('endDate', endDate.format('YYYY-MM-DD'));
            $('#dateRange').val(setDate);
          }
      });

  });

}
loadLeaveCalendar();


$(document).on('click','#dateRange', function (e) {
    $('.calendarLeave ').removeClass('maxHeightHide');
});


$('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {

  //console.log(picker.startDate.format('YYYY-MM-DD'));
  //console.log(picker.endDate.format('YYYY-MM-DD'));
});


function loadAddNewLeaveCalendar(){

     $('.leaveCalendar').multiDatesPicker('resetDates');
     $(".leaveCalendar").datepicker( "destroy" );
     $(".leaveCalendar").multiDatesPicker({
       minDate: 0,
       onSelect:function(data, event){
        
        var getValues = [];
        var checkValue = $('#dateRange').val();
        var checkCounter = 0
        $('.calendarLeave table td.ui-state-highlight').each(function(){
          checkCounter++;
          var getDate = parseInt($(this).find('a').text());
          var getMonth = parseInt($(this).attr('data-month'));
          var getYear = parseInt($(this).attr('data-year'));
          getMonth++
          var setLeaveDate;
          if(getDate <= 9)
          {
            getDate = "0" + getDate;
          }
          if(getMonth <= 9)
          {
            getMonth = "0" + getMonth;
          }
          setLeaveDate = getYear + "-" + getMonth + "-" + getDate + ' ';
          
          getValues.push(setLeaveDate);
          if(checkCounter > 1)
          {
            $('.calendarLeave').addClass('maxHeightHide');
            return false
          }
            
        });
        
        $('#dateRange').attr('value', getValues);    
        $('#dateRange').val(getValues);
        
       }
     });

     $(".leaveCalendar").focus();

}

setTimeout(function(){ 
  loadAddNewLeaveCalendar()
}, 2000);

$(document).on('click','.selected-text', function (e) {
    $('.calendarLeave ').addClass('maxHeightHide');
});

$(document).on('click','.ui-datepicker-next, .ui-datepicker-prev', function (e) {
    return false
});


$(document).on('click', function(event){
    var container = $(".newLeaveContainer");
    if (!container.is(event.target) &&            
        container.has(event.target).length === 0)
        {
            $('.calendarLeave ').addClass('maxHeightHide');
        }
    
});// End


/*------------------------------------------------------------------*/
/*-------------------------- End Leave Code ----------------------- */
/*------------------------------------------------------------------*/

/*------------------------------------------------------------------*/
/*-------------------------- Start Close Lead --------------------- */
/*------------------------------------------------------------------*/

// Close Lead Popup

// Reloading Calendar
function SetCalendarCloseLead(calendarDate){

    $('.closeLeadCalendar').multiDatesPicker('resetDates');
    $('.closeLeadCalendar').multiDatesPicker('destroy');
    $('.closeLeadCalendar').multiDatesPicker({
        defaultDate : calendarDate,
        onSelect:function(data, event){

        var getValues = [];
        var el = $(this);
        var checkValue = el.closest('.closeLeadPopup').find('.closeLeadDate').val();

        el.closest('.closeLeadPopup').find('.calendarClose table td.ui-state-highlight').each(function(){
          
          var getDate = parseInt($(this).find('a').text());
          var getMonth = parseInt($(this).attr('data-month'));
          var getYear = parseInt($(this).attr('data-year'));
          getMonth++
          var setLeaveDate = getMonth+'/'+getDate+'/'+getYear;
          getValues.push(setLeaveDate);
        });
        
        $(this).closest('.closeLeadPopup').find('.closeLeadDate').attr('value', getValues); 
  
        $(this).closest('.closeLeadPopup').find('.closeLeadDate').val(getValues);
        
      }
    });
}

// Open calendar for close lead
$(document).on('click','.closeLeadDate', function (e) {
  var el = $(this);
    el.next('.calendarClose ').removeClass('maxHeightHide');
    el.next('.calendarClose ').find(".closeLeadCalendar").datepicker( "destroy" );
    el.next('.calendarClose ').find(".closeLeadCalendar").multiDatesPicker({
      minDate: 0,
      onSelect:function(data, event){ 
      
        var getDate = event.currentDay;
        var getMonth = event.currentMonth;
        getMonth++;
        var getYear = event.currentYear;
        var setLeaveDate = getMonth+'/'+getDate+'/'+getYear;
        var checkValue = $(this).val();

        el.closest('.closeLeadPopup').find('.closeLeadCalendar').multiDatesPicker('resetDates');
        el.closest('.closeLeadPopup').find('.closeLeadDate').attr('value', setLeaveDate); 
        el.closest('.closeLeadPopup').find('.closeLeadDate').val(setLeaveDate);
        el.closest('.closeLeadPopup').find('.calendarClose').addClass('hide');
        el.closest('.closeLeadPopup').find('.calendarClose').addClass('maxHeightHide');
        setTimeout(function(){ 
            el.closest('.closeLeadPopup').find('.calendarClose').removeClass('hide');
        }, 500);

      }
    });
});

function loadCloseLeadError(){
  $('.closeLeadError').removeClass('opacity0');
  setTimeout(function(){ 
    $('.closeLeadError').addClass('opacity0');
}, 3000);

}

// Save close lead
$(document).on('click','.btn-saveCloseLead', function (e) {
    var getStatusValue = $(this).closest('.closeLeadPopup').find('.closeStatus').attr('value');
    var getDatesValue = $(this).closest('.closeLeadPopup').find('.closeLeadDate').attr('value');
    var getLeadId = $(this).closest('.closeLeadPopup').attr('leadid');
    if(getStatusValue == "All" || getDatesValue == "")
    {
        loadCloseLeadError();
        return false
    }
    else
    { 
       
        $lead_status = getStatusValue;
        $lead_id =  getLeadId;
        $lead_date = getDatesValue;
        $.ajax({
        type: "POST",
        url: "/updateleadstatusFromDashboard",
        data: {lead_statusId : $lead_id , lead_status : $lead_status , lead_close_date : $lead_date },
        success: function (data) {
            
            loadLeads(); 
            
          }
        });  
        
        $('.closeLeadClick').removeClass('active');
        $('.closeLeadPopup').slideUp(50);      
    }

});

// Cancel close lead
$(document).on('click','.btn-cancelCloseLead', function (e) {
  $('.closeLeadClick').removeClass('active');
    $('.closeLeadPopup').slideUp(50);
});

// calendar short cut option selection

$(document).on('click','.closeLeadOptions span', function (e) {

    var el = $(this);
    el.closest('.closeLeadPopup').find('.closeLeadDate').attr('value', el.attr('value')); 
    el.closest('.closeLeadPopup').find('.closeLeadDate').val(el.attr('value'));
    el.closest('.calendarClose ').addClass('hide');
    el.closest('.calendarClose ').addClass('maxHeightHide');
    setTimeout(function(){ 
        el.closest('.calendarClose ').removeClass('hide');
    }, 500);
});

// Close outside click

$(document).on('click', function(event){
    var container = $(".lead-list li");
    if (!container.is(event.target) &&            
        container.has(event.target).length === 0)
        {
            $('.closeLeadPopup').slideUp(50);
            $('.closeLeadClick').removeClass('active'); 
        }
    
});// End


function fullTimeFormat(getTime)
{
   if(getTime == "8:00 - 9:00 AM")
    { return "8:00 AM - 9:00 AM" }
    else if(getTime == "9:00 - 10:00 AM")
    { return "9:00 AM - 10:00 AM" }
    else if(getTime == "10:00 - 11:00 AM")
    { return "10:00 AM - 11:00 AM" }
    else if(getTime == "11:00 - 12:00 PM")
    { return "11:00 AM - 12:00 PM" }
    else if(getTime == "12:00 - 1:00 PM")
    { return "12:00 PM - 1:00 PM" }
    else if(getTime == "1:00 - 2:00 PM")
    { return "1:00 PM - 2:00 PM" }
    else if(getTime == "2:00 - 3:00 PM")
    { return "2:00 PM - 3:00 PM" }
    else if(getTime == "3:00 - 4:00 PM")
    { return "3:00 PM - 4:00 PM" }
    else if(getTime == "4:00 - 5:00 PM")
    { return "4:00 PM - 5:00 PM" }
    else
    { return "5:00 PM - 6:00 PM" }

}

/*------------------------------------------------------------------*/
/*-------------------------- End Close Lead ----------------------- */
/*------------------------------------------------------------------*/



// New Leave

//http://crm.diamond:8000/leave/ajaxGetUserDetailForLeave
 
    function GetAllAgents() {
        $.ajax({
            type: "GET",
            url: "/leave/ajaxGetUserDetailForLeave",
            data: {},
            success: function (data) {
                
                // convert json into Array
                var parsed = '';                 
                try{                      
                parsed = JSON.parse(data);               
                }                
                catch(e)                
                {                    
                return false;                  
                }

                var arr = [];
                
                for(var x in parsed){
                  arr.push(parsed[x]);
                }

                // Append Agent list into agent dropdown

                var dropdownList = "";

                var Adding = true;
                for(i=0 ; i < arr.length; i++)
                {
                    var getUserimage = arr[i].image;
                    var getUserName = arr[i].UserName;
                    if(getUserimage == "" || getUserimage == null)
                    {
                        getUserimage = 'sampleUser.png';
                    }
                        dropdownList += '<li><a href="javascript:;" id="'+arr[i].user_id+'" value="'+getUserName+'"><span><img class="pull-left" src="/profile_image/'+getUserimage+'"><span><div><label>Next in line:</label><label>'+getUserName+'</label></div></span></span></a></li>';
                }

                setTimeout(function(){ 
                        
                  $('.assignToDivLeave ul.dropdownOptions').html(dropdownList);

                }, 1500);
            }
        });      
    }

    GetAllAgents()
    
/*------------------------------------------------------------------*/
/*-------------------------- Get All Leaves------------------------- */
/*------------------------------------------------------------------*/

//Add new View Screen

$(document).on('click','.leadUserName', function (e) {
    var getLeadId = $(this).closest('.userLeadId').attr('userleadid');    
    
    $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetLeadDetailForLeadPage",
            data: {lead_id:getLeadId},
            success: function (data) {
                
              var parsed = '';
              
              try
              {
                parsed = JSON.parse(data);                  
              }
              catch(e)
              {                  
               return false;                    
              }
            
                // Append Agent list into agent dropdown
                $('.editDetails').attr('lead-id',parsed.id)
                if(parsed.lead_status == "Closed")
                {
                    $('.editDetails').addClass('disabled');
                }
                else
                {
                    $('.editDetails').removeClass('disabled');
                }
                var html = "";
               if(parsed[0].LeadTitle == null)
                {parsed[0].LeadTitle = ''}
               html += "<p><label>Title:</label><label>" + parsed[0].LeadTitle + "</label></p> ";

               if(parsed[0].LeadGender == null)
                {parsed[0].LeadGender  = ''}
               html += "<p><label>Gender:</label><label>" + parsed[0].LeadGender + "</label></p> ";

               if(parsed[0].LeadFirst_name == null)
               { html += "<p><label>First Name:</label><label></label></p> "; }
               else
               { html += "<p><label>First Name:</label><label>" + parsed[0].LeadFirst_name.replace(/'/g, '"') + "</label></p> "; }

               if(parsed[0].LeadLast_name == null)
               { html += "<p><label>First Name:</label><label></label></p> "; }
               else
               { html += "<p><label>First Name:</label><label>" + parsed[0].LeadLast_name.replace(/'/g, '"') + "</label></p> "; }

               if(parsed[0].LeadMobile == null)
               {parsed[0].LeadMobile = ''} 
               html += "<p><label>Phone Number:</label><label>" + parsed[0].LeadMobile + " </label></p> ";

               html += "<p><label>Email:</label><label>" + parsed[0].LeadEmail + " </label></p> ";

               if(parsed[0].CustomerCountry_id == null)
                {parsed[0].CustomerCountry_id = ''}
               html += "<p><label>Country:</label><label>" + parsed[0].CustomerCountry_id + " </label></p> ";

               if(parsed[0].CustomerCountry_id == 'Australia')
               {
                  html += "<p><label>State:</label><label>" + parsed[0].StateShortCode + " </label></p> ";
               }

               if(parsed[0].CustomerAddress == null)
                {parsed[0].CustomerAddress = ''}
               html += "<p><label>Full Address:</label><label>" + parsed[0].CustomerAddress + " </label></p> ";

               if(parsed[0].LeadSource == null)
                {parsed[0].LeadSource = ''}
               html += "<p><label>Lead Source:</label><label>" + parsed[0].LeadSource + " </label></p> ";

               if(parsed[0].LeadPreferredContact_method == null)
                {parsed[0].LeadPreferredContact_method = ''}
               html += "<p><label>Preferred method of contact:</label><label>" + parsed[0].LeadPreferredContact_method + " </label></p> ";

               html += "<p><label>Product:</label><label>" + parsed[0].product_title + " </label></p> ";

               if(parsed[0].how_heard_title == null)
                {parsed[0].how_heard_title =''}
               html += "<p><label>How did they hear about us:</label><label>" + parsed[0].how_heard_title + " </label></p> ";

               if(parsed[0].LeadReferredCustomerName == null)
                {parsed[0].LeadReferredCustomerName = ''}
               html += "<p><label>Referral by customer:</label><label>" + parsed[0].LeadReferredCustomerName + " </label></p> ";

               

               //html += "<p><label>Referral:</label><label>" + parsed[0].CustomerAddress + " </label></p> ";

               if(parsed[0].LeadLookingFor == null)
                {parsed[0].LeadLookingFor = ''}
               html += "<p><label>What they are looking for:</label><label>" + parsed[0].LeadLookingFor + " </label></p> ";

               

               if(parsed[0].LeadBudget == '0')
               {html += "<p><label>Budget:</label><label></label></p> ";}
               else
               {html += "<p><label>Budget:</label><label>" + parsed[0].LeadBudget + "</label></p> ";}
               
               html += "<p><label>Assign To:</label><label>" + parsed[0].LeadOwnerName + "</label></p> ";
               
               if(parsed[0].LeadReference == null)
                {parsed[0].LeadReference =''}
               html += "<p><label>Reference Product:</label><label>" + parsed[0].LeadReference + "</label></p> ";

               html += "<p><label>Special Instructions:</label><label>" + parsed[0].LeadSpecialInstructions + " </label></p> ";

               html += "<p><label>Lead Status:</label><label>" + parsed[0].LeadStatus + "</label></p> ";
               
                $('.leadDeailInnerContainer div').html(html);
                $('.leadsContainer').addClass('hide');
                $('.leadDeailContainer').removeClass('hide');

            }

        });    
        
});


// open close lead container
$(document).on('click','.closeLeadClick', function (e) {
       
      var el =$(this);
      $('.calendarClose ').addClass('maxHeightHide');
      if(el.hasClass('active'))
      { 
        el.removeClass('active');
        el.next('.closeLeadPopup').slideUp(50);
      } 
      else
      { 
        $('.closeLeadPopup').hide();
        $('.closeLeadClick').removeClass('active');
        el.addClass('active');
        el.next('.closeLeadPopup').slideDown(300);
      } 

});


// Get Time slots 
function getTimeSlot(getTime) {
  
  if(getTime == "8:00 - 9:00 AM")
  { return "8-9" }
  else if(getTime == "9:00 - 10:00 AM")
  { return "9-10" }
  else if(getTime == "10:00 - 11:00 AM")
  { return "10-11" }
  else if(getTime == "11:00 - 12:00 PM")
  { return "11-12" }
  else if(getTime == "12:00 - 1:00 PM")
  { return "12-1" }
  else if(getTime == "1:00 - 2:00 PM")
  { return "1-2" }
  else if(getTime == "2:00 - 3:00 PM")
  { return "2-3" }
  else if(getTime == "3:00 - 4:00 PM")
  { return "3-4" }
  else if(getTime == "4:00 - 5:00 PM")
  { return "4-5" }
  else
  { return "5-6" }
    
}
function getTimeSlotFull(getTime) {
  
  if(getTime == "8-9")
  { return "8:00 AM - 9:00 AM" }
  else if(getTime == "9-10")
  { return "9:00 AM - 10:00 AM" }
  else if(getTime == "10-11")
  { return "10:00 AM - 11:00 AM" }
  else if(getTime == "11-12")
  { return "11:00 AM - 12:00 PM" }
  else if(getTime == "12-1")
  { return "12:00 PM - 1:00 PM" }
  else if(getTime == "1-2")
  { return "1:00 PM - 2:00 PM" }
  else if(getTime == "2-3")
  { return "2:00 PM - 3:00 PM" }
  else if(getTime == "3-4")
  { return "3:00 PM - 4:00 PM" }
  else if(getTime == "4-5")
  { return "4:00 PM - 5:00 PM" }
  else
  { return "5:00 PM - 6:00 PM" }
    
}

function getBookingTime(getTime, bookingStart, Duation) {
  var time = '';
  if(getTime == "8-9")
  { time =  "8:" }
  else if(getTime == "9-10")
  { time =  "9:" }
  else if(getTime == "10-11")
  { time =  "10:" }
  else if(getTime == "11-12")
  { time =  "11:" }
  else if(getTime == "12-1")
  { time =  "12:" }
  else if(getTime == "1-2")
  { time =  "1:" }
  else if(getTime == "2-3")
  { time =  "2:" }
  else if(getTime == "3-4")
  { time =  "3:" }
  else if(getTime == "4-5")
  { time =  "4:" }
  else
  { time =  "5:" }
  
  if(bookingStart == "0")
    { time = time + '00' }
  else if(bookingStart == "15") 
    { time = time + '15' }
  else if(bookingStart == "30")
    { time = time + '30' }
  else
    { time = time + '45' }
  return time
    
}


    function GetLoginUserDetail() {
        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetUserLoginDetail",
            data: {},
            success: function (data) {
              
                var getData = data;
                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)                  
                {                   
                return false;                  
                }
                if(parsed.role_id == 1)
                { window.adminUser = true; }
                else
                { window.adminUser = false; }

            }

        });    
        
    }
    
    GetLoginUserDetail();
    
    function getSuffix3(getDay)
    {
        var suffix = ''
        if(getDay == 1 || getDay == 21 || getDay == 31)
        {
            suffix = 'st'
        }
        else if(getDay == 2 || getDay == 22)
        {
            suffix = 'nd'   
        }
        else if(getDay == 3 || getDay == 23)
        {
            suffix = 'rd' 
        }
        else
        {
            suffix = 'th'    
        }
        return suffix;
    }

   /*------------------------------------------------------------------*/
   /*--------------------Start GetCountries Ajax List----------------- */
   /*------------------------------------------------------------------*/
   
    

    function GetCountriesList2() {
        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetCountriesList",
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }


                var setHtml2 = '';
                for (var i = 0; i < parsed.length; i++) {
                  var countryName = parsed[i].country_name;
                  setHtml2 += "<option value='"+countryName+"'>"+countryName+"</option>";

                }
                $('#combobox, #partnerCombobox').html(setHtml2);
                $('.ui-autocomplete li:first-child a').trigger('click');
                $('.ui-autocomplete li:first-child').trigger('click');
            }

        });    
        
    }

    //GetCountriesList2();

    setTimeout(function(){           

       $(document).ready(function(){

            (function($) {
              $.widget("ui.combobox", {
                  _create: function() {
                      var input, self = this,
                          select = this.element.hide(),
                          selected = select.children(":selected"),
                          value = selected.val() ? selected.text() : "",
                          wrapper = $("<span>").addClass("ui-combobox").insertAfter(select);

                      input = $("<input>").appendTo(wrapper).val(value).addClass("ui-state-default").autocomplete({
                          delay: 0,
                          minLength: 0,
                          source: function(request, response) {

                              var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                              response(select.children("option").map(function() {
                                  var text = $(this).text();
                                  if (this.value && (!request.term || matcher.test(text))) return {
                                      label: text.replace(
                                      new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "$1"),
                                      value: text,
                                      option: this
                                  };
                              }));
                          },
                          select: function(event, ui) {
                              
                              ui.item.option.selected = true;
                              self._trigger("selected", event, {
                                  item: ui.item.option
                              });
                              
                              var getCountry = ui.item.label;
                              var isThisPartnerCountry = $(this).closest('.countryDiv').hasClass('partnerCountryDiv');
                              if(isThisPartnerCountry)
                              {

                                  $('#customerCountryName').attr('value', getCountry);
                                  $('.partnerStateError').addClass('opacity0');
                                  if(getCountry == 'Australia')
                                  { $('.partnerStateDiv').removeClass('hide'); }
                                  else
                                  { $('.partnerStateDiv').addClass('hide'); }

                              }
                              else
                              {

                                  $('#countryName').attr('value', getCountry);
                                  $('.stateerror').addClass('opacity0');
                                  if(getCountry == 'Australia')
                                  { $('.stateDiv').removeClass('hide'); }
                                  else
                                  { $('.stateDiv').addClass('hide'); }  

                              }
                              
                              
                          },
                          change: function(event, ui) {
                            debugger
                              if (!ui.item) {
                                  var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this).val()) + "$", "i"),
                                      valid = false;
                                  select.children("option").each(function() {
                                      if ($(this).text().match(matcher)) {
                                          this.selected = valid = true;
                                          return false;
                                      }
                                  });
                                  if (!valid) {
                                
                                      // remove invalid value, as it didn't match anything
                                      $(this).val("");
                                      return false;
                                  }
                              }
                          }
                      }).addClass("ui-widget ui-widget-content ui-corner-left");

                      input.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                        return $( "<li>" )
                          .data( "ui-autocomplete-item", item )
                          .append( "<a>" + item.label+"</a>" )
                          .appendTo( ul );
                      };

                      $("<button>").attr("tabIndex", -1).attr("title", "Show All Items").appendTo(wrapper).button({
                          icons: {
                          //    primary: "ui-icon-triangle-1-s"
                          },
                          text: false
                      }).removeClass("ui-corner-all").addClass("ui-corner-right ui-button-icon ui-combobox-button").click(function() {
                          // close if already visible
                          
                          if (input.autocomplete("widget").is(":visible")) {
                              input.autocomplete("close");
                              return;
                          }

                          // work around a bug (likely same cause as #5265)
                          $(this).blur();

                          // pass empty string as value to search for, displaying all results
                          input.autocomplete("search", "");
                          input.focus();
                          return false;
                      });
                  },

                  destroy: function() {

                  }
              });
          })(jQuery);

    }, 0);

    $(function() {
        $("#combobox, #partnerCombobox").combobox({
           classes: {
              "ui-autocomplete": "your-custom-class",
          },
            selected: function(event, ui) {
                $('#log').text('selected ' + $("#combobox, #partnerCombobox").val());
            }
        });


        // Look up Api

        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetCustomerOnLookup", 
            data: {},
            success: function (data) {

                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }
                var htmlDate = "";
                

                for (var i = 0; i < parsed.length; i++) {
                  htmlDate += "<tr>";
                  htmlDate += "<td id='"+ parsed[i].id +"' value='"+ parsed[i].user_name +"'>" + parsed[i].user_name +"</td>";
                  htmlDate += "</tr>";
                }
                
                $("#datatable tbody").html(htmlDate);

                 var table = $('#datatable').DataTable( {"lengthChange": false,});
                  $('.customerList input').attr('placeholder', 'Type to search...');
                  $('#datatable tbody').on( 'click', 'tr', function () {
                    
                    var getVal = $(this).find('td').attr('value');
                    var getID = $(this).find('td').attr('id');
                    $('#onlyReferral').val(getVal);
                    $('#onlyReferral').attr('referenceCustomerId',getID);
                    $('.showLookups').slideUp();
                    $('#onlyReferral').closest('.relative').find('span').show();
                    setTimeout(function(){ $('.showLookups').addClass('hide'); }, 500);
                  });
                  
                  //window.list = parsed;
                  
            }
        });


        // Get user by Name Api

        $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetCustomerByName", 
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }
                window.list = parsed;
                console.log(parsed); 
            }
        });

    });
    
   });
  
  
  function getCustomerByName()
  {
    $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetCustomerByName", 
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }
                window.list = parsed;
                console.log(parsed); 
            }
        });
  }





   /*------------------------------------------------------------------*/
   /*---------------------- Start Search Area Code ------------------- */
   /*------------------------------------------------------------------*/


    // Search Record Selection
    $(document).on('click', '.resultFields', function () {
      var el = $(this);
      var customerId = el.attr('customerId');
      var leadId = el.attr('leadId');
      var resultUrl = "/dashboard/ajaxGetCustomerById?customer_id="+customerId+"&lead_id="+leadId;
      if(leadId == '')
        { resultUrl = "/dashboard/ajaxGetCustomerById?customer_id="+customerId; }
      $.ajax({
            type: "GET",
            url: resultUrl, 
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }
                // Check If Opprtunity Exists
                if(parsed.Customer.OpportunityStatus == '1')
                { 
                  //var opportunityId = parsed.Customer.Opportunity.OpportunityId;
                  //window.location.href = '/opportunitydetails/'+opportunityId;
                  var opportunityId = parsed.Customer.Customer.Customer_id;
                  window.location.href = '/customerdetails/'+opportunityId;
                }
                else if(parsed.Customer.LeadStatus == '1')
                { popuLateLead(parsed.Customer) }
                else
                {
                  $('.searchField').val(parsed.Customer.Customer.CustomerFirst_name + ' ' + parsed.Customer.Customer.CustomerLast_name )
                  $("#searchResults").html(' ');
                  $('.dialogeBox.creatingCustomer').removeClass('hide');
                  window.createNewCustomer = parsed.Customer;
                }
                return false; 
            }
        });
    });

    /*=====================================*/

    function popuLateLead(lead)
    {
      showMainLoading();
      // Populating Records

      $("#searchResults").html(' ');
      $('#customerId').attr('customerid','0');
      $(lead.Customer.CustomerTitle == null)
      {lead.Customer.CustomerTitle = 'Mr'}
      $('.dropdown.title .dropdownOptions li a[value="'+lead.Customer.CustomerTitle+'"]').trigger('click');              // title gender   
      $('.basicInfo .firstname').val(lead.Customer.CustomerFirst_name);                                                  // first name   
      $('.basicInfo .lastname').val(lead.Customer.CustomerLast_name);                                                    // last name
      if(lead.Customer.CustomerMobile == null)
      {lead.Customer.CustomerMobile = ''}
      else
      { $('.basicInfo .phonenumber').attr('readOnly', 'readOnly'); }
      $('.basicInfo .phonenumber').val(lead.Customer.CustomerMobile).attr('passon','true');                              // phone
      $('#email').val(lead.Customer.CustomerEmail);
      if(lead.Customer.CustomerEmail == null)
      {lead.Customer.CustomerEmail = ''}
      else
      { $('#email').attr('readOnly', 'readOnly'); }                                                                      // email
      $('#email').addClass('popuplatedemail');
      $('#email').attr('leadId',lead.Lead.Lead_id);                                                                      // email
      $('.thisLeadId').attr('leadid',lead.Lead.Lead_id); 
      if(lead.Customer.CustomerAddress ==  null)
      {$('#fullAddress').val('');}
      else
      {$('#fullAddress').val(lead.Customer.CustomerAddress);}                                                               // address
      $('#countryName').attr('value',lead.Customer.CustomerCountry_id);                                                     // country
      if(lead.Customer.CustomerCountry_id == null)
      {
        setTimeout(function(){ 
          $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
          $('#countryName').attr('value','Australia'); 
       }, 2000);
      }
      else
      {
        setTimeout(function(){ 
          $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val(lead.Customer.CustomerCountry_id);
          $('#countryName').attr('value',lead.Customer.CustomerCountry_id);
        }, 2000);
      }

      $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val(lead.Customer.CustomerCountry_id); 
      if(lead.Customer.CustomerState_id != null)                                                                            // state
      {
        $('.stateDiv .dropdown.State .dropdownOptions li a[stateid="'+lead.Customer.CustomerState_id+'"]').trigger('click');
      }        
          // state
       if(lead.Customer.CustomerCountry_id == 'Australia' || lead.Customer.CustomerCountry_id == null)                       // country                                                                  
       {
        $('.stateDiv').removeClass('hide');
       }
       else
       {
        $('.stateDiv').addClass('hide');
       }

       $('.emailexists').addClass('opacity0').removeClass('green');
       $('.redCross, .redGreen').addClass('hide');
       $('.basicInfo .requiredError, .emailDiv .requiredError, .firstError, .emailexists, .emailDiv .error ').addClass('opacity0');
       if(lead.Customer.CustomerSource != null)
       {
          $('.dropdown.CommunicationMethod .dropdownOptions li a[value="'+lead.Customer.CustomerSource+'"]').trigger('click');
       }
       if(lead.Lead.LeadPreferredContact_method != null)
       {
          $('.dropdown.preferredMethod .dropdownOptions li a[value="'+lead.Lead.LeadPreferredContact_method+'"]').trigger('click');
       }
       $('.dropdown.product .dropdownOptions li a[value="'+lead.Lead.LeadProduct_title+'"]').trigger('click');
       $('.dropdown.referral .dropdownOptions li a[value="'+lead.Lead.LeadHow_heard_title+'"]').trigger('click');
       if(lead.Lead.LeadReferredbyCustomer != null)
       {
        $('#onlyReferral').attr('referencecustomerid',lead.Lead.LeadReferredbyCustomer);
        $('#onlyReferral').val(lead.Lead.LeadCustomerFullName);
       }
       if(lead.Lead.LeadLookingFor != null)
       {
        $('#specify_requirements').val(lead.Lead.LeadLookingFor);
        $('#specify_requirements').prev('span').slideDown(150);
       }
       if(lead.Lead.LeadSpecialInstructions != null)
       {
        $('#specialinstructions').val(lead.Lead.LeadSpecialInstructions);
       }
       //budget
       $('.dropdown.budget .dropdownOptions li a[value="'+lead.Lead.LeadBudget+'"]').trigger('click');
       if(lead.Lead.LeadReference == null)
        {lead.Lead.LeadReference = ''}
       $("#referrenceDropdown").val(lead.Lead.LeadReference);
       setTimeout(function(){ 
          $('.additional-details .dropdown.assignToDiv .dropdownOptions li a[id="'+lead.Lead.LeadOwnerId+'"]').click();
          $('.additional-details .dropdown.assignToDiv .dropdownOptions').hide();
          $('.additional-details .dropdown.assignToDiv .dropdownOptions .btn-skip2').click();
          $('.dropdown.assignToDiv').prev('span').slideDown(150);
          $('.dropdownOptions').hide();
       }, 1000)

       $('.dropdownOptions').hide();
       $('.formfields input').each( function () {
          if($(this).val().length > 0)
          {
            $(this).prev('span').slideDown(150);
          }    
        });// End

       $('.additional-details input').each( function () {
          if($(this).val().length > 0)
          {
            $(this).prev('span').slideDown(150);
            $(this).prev('a').prev('span').slideDown(150);
          }    
        });// End

       
    }

    /*=====================================*/

    // Create Customer from Search
    $(document).on('click', '.yesCreateCustomer', function () {

       $('.thisLeadId').attr('leadid','');
       $('.basicInfo').html(window.getBasicInfo);
       $('.additional-details').html(window.getAdditionalInfo);
       $('.dialogeBox.creatingCustomer').addClass('hide');
       //$('.ShowPopup').removeClass('topShow');
       var customer = window.createNewCustomer;
       $("#customerId").attr('customerId',customer.Customer.Customer_id);
       
       $("#searchResults").html(' ');
       $(customer.Customer.CustomerTitle == null)
      {customer.Customer.CustomerTitle = 'Mr'}
      $('.dropdown.title .dropdownOptions li a[value="'+customer.Customer.CustomerTitle+'"]').trigger('click');               // title gender   
      $('.basicInfo .firstname').val(customer.Customer.CustomerFirst_name);                                                   // first name   
      $('.basicInfo .lastname').val(customer.Customer.CustomerLast_name);                                                     // last name
      
      if(customer.Customer.CustomerMobile == null)                                                                            // phone
      {
        customer.Customer.CustomerMobile ='';
      }
      else
      {
        $('.basicInfo .phonenumber').attr('readOnly', 'readOnly');
        
      }
      $('.basicInfo .phonenumber').val(customer.Customer.CustomerMobile);                                                             

      if(customer.Customer.CustomerEmail == null)                                                                             // email
      { customer.Customer.CustomerEmail = ''; }
      else
      { $('#email').attr('readOnly', 'readOnly'); }
                                                                                                                              
      $('#email').val(customer.Customer.CustomerEmail);                                                                       
      $('#email').addClass('newcustomerLead'); 

      if(customer.Customer.CustomerAddress ==  null)
      {$('#fullAddress').val('');}
      else
      {$('#fullAddress').val(customer.Customer.CustomerAddress);} 
      if(customer.Customer.CustomerCountry_id == null)                                                                        // country
      {
        setTimeout(function(){ 
          $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
          $('#countryName').attr('value','Australia'); 
          $('.emailexists').addClass('green');
       }, 2000);
      }
      else
      {
        setTimeout(function(){ 
          $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val(customer.Customer.CustomerCountry_id);
          $('#countryName').attr('value',customer.Customer.CustomerCountry_id);
          $('.emailexists').addClass('green');
        }, 2000);

      }                                                                                                                         // address
                                                     
      $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val(customer.Customer.CustomerCountry_id);         // country
      $('.stateDiv .dropdown.State .dropdownOptions li a[stateid="'+customer.Customer.CustomerState_id+'"]').trigger('click');  // state
       if(customer.Customer.CustomerCountry_id == 'Australia' ||  customer.Customer.CustomerCountry_id == null)                 // state
       {
        $('.stateDiv').removeClass('hide');
       }
       else
       {
        $('.stateDiv').addClass('hide');
       }
       $('.emailexists').addClass('opacity0').removeClass('green');
       $('.redCross, .redGreen').addClass('hide');
       $('.basicInfo .requiredError, .emailDiv .requiredError, .firstError, .emailexists, .emailDiv .error ').addClass('opacity0');
       $('.formfields input').each( function () {
          if($(this).val().length > 0)
          {
            $(this).prev('span').slideDown(150);
          }    
        });// End
       $('#email').addClass('popuplatedemail');
       $('#phonenumber').focusin();
       $('#phonenumber').focusout();
    });// End

    /*=====================================*/

    // Cancel Create Customer popup
    $(document).on('click', '.NoCancelCustomer', function () {
      $('.dialogeBox.creatingCustomer').addClass('hide');
      $('.searchField').val('');
    });// End

    // Search Autocomplete
    $(document).on('keyup', '.searchField', function () {
        mainSearch()
    });// End

    $(document).on('click', '.searchArea .icon-search', function () {
        mainSearch()
    });// End

    function mainSearch()
    {
      var getValue = $('.searchField').val();
      getValue = getValue.toLowerCase();
      var getLength = getValue.length;
      var arr = [];
      if(getLength > 2)
      {
        var parsed = window.searchData;

        for (var i = 0; i < parsed.length; i++) {
            
            var name = parsed[i].name; 
            name = name.toLowerCase();
            var email = parsed[i].email;
            if(email == null)
            {email = '';}
            else
            {email = email.toLowerCase();}
            var phone = parsed[i].mobile;
            if(phone == null)
            {phone = '';}
            else
            {phone = phone.toLowerCase();}
            
             if (name.indexOf(getValue) > -1 || email.indexOf(getValue) > -1 || phone.indexOf(getValue) > -1) {
               arr.push(parsed[i]);
             }
             else {}
          }
      }
      else
      {
        $("#searchResults").html(' ');
        return false;
      }
      var filteredArray = arr;
      var setHtml = '';
        for (var i = 0; i < filteredArray.length; i++) 
        {
          var bookingDate = filteredArray[i].create_date;                               // Booking Date
          var formattedDate = moment(bookingDate).add(0, 'M').format('DD.MM.YYYY');     // Formatted Date
          var productName = filteredArray[i].product_title;
          if(productName ==  null)
          {productName = ''}                                                            // Product Name
          var productIconName = productIcons(productName); 
          if(productName == '')
          {productIconName = productIconName + 'opacity0'}                                   // Get Icon
          var getFullObj = JSON.stringify(filteredArray[i]);
          var mobileNumber = '';
          if(filteredArray[i].mobile == null)
            {filteredArray[i].mobile = ''}
          if(filteredArray[i].lead_id == null)
            {filteredArray[i].lead_id = ''}
          setHtml += "<div class='resultFields full pointer' dataObj='"+getFullObj+"' leadId='"+ filteredArray[i].lead_id +"'  customerId='"+ filteredArray[i].id +"'>";
            setHtml += "<span class='d-i-b wd-67 v-a-t align-center'><i class='"+productIconName+ " fs-36 color-darkBlue'></i></span>";
            setHtml += "<div class='searchContent d-i-b v-a-t half-pad-top'>";
              setHtml += "<h1 class='fs-15 lh-22'>"+filteredArray[i].name+"</h1>"; // Name
              setHtml += "<div>";
                setHtml += "<div class='half lh-16'><label class='d-i-b color-darkBlue'>Created:</label><span>"+formattedDate+"</span></div>"; // Created
                setHtml += "<div class='half lh-16'><label class='d-i-b color-darkBlue'>Product:</label><span>"+productName+"</span></div>"; // Product
                //setHtml += "<div class='half lh-16'><label class='d-i-b color-darkBlue'>Product:</label><span>"+filteredArray[i].product+"</span></div>"; // Product
                setHtml += "<div class='half lh-16'><label class='d-i-b color-darkBlue'>Mobile:</label><span>"+filteredArray[i].mobile+"</span></div>"; // Mobile
                setHtml += "<div class='half lh-16'><label class='d-i-b color-darkBlue'>Email:</label><span>"+filteredArray[i].email+"</span></div>"; // Email
              setHtml += "</div>";
            setHtml += "</div>";
          setHtml += "</div>";

        }

        if(filteredArray.length > 0)
        {
          $("#searchResults").html(setHtml);
        }
        else
        {
          $("#searchResults").html('<span class="double-gap-top padded full bold">No record found. Please create new lead.</span> ');
        }
    }

    // Get product icons for search autocomplete
    function productIcons(productName)
    {
      var iconName = "";
      if(productName == "Engagement Ring")
      {
        iconName = 'icon-engRing';
      }
      else if(productName == "Earrings")
      {
        iconName = 'icon-earrings';
      }
      else if(productName == "Bracelet")
      {
        iconName = 'icon-bracelet';
      }
      else if(productName == "Wedding Band")
      {
        iconName = 'icon-weddingBrand';
      }
      else if(productName == "Eternity Band")
      {
        iconName = 'icon-diamond';
      }
      else if(productName == "Loose Diamond")
      {
        iconName = 'icon-diamond';
      }
      else if(productName == "Dress Rings")
      {
        iconName = 'icon-dressRings';
      }
      else if(productName == "Pendant")
      {
        iconName = 'icon-pendant';
      }
      else if(productName == "Timepiece")
      {
        iconName = 'icon-watch';
      }
      else if(productName == "Custom Jewellery")
      {
        iconName = 'icon-diamond';
      }
      else if(productName == "Loose Gemstone")
      {
        iconName = 'icon-diamond';
      }
      else
      { iconName = 'icon-diamond' }
      return iconName;
    }

    // Get all lead record for search autocomplete
    function getSearchData()
    {
          $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetDataForSearch", 
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }

                window.searchData = parsed;
              
            }

        });
    }
    setTimeout(function(){ 
        getSearchData()
    }, 2000);
    


    $(document).on('click', function(event){
      var container = $(".searchContainer");
      if (!container.is(event.target) &&            
          container.has(event.target).length === 0)
          {
              $("#searchResults").html(' ');
          }
      
  });// End


   /*------------------------------------------------------------------*/
   /*----------------------- End Search Area Code -------------------- */
   /*------------------------------------------------------------------*/



  function pickUpAgents (){
    $.ajax({
            type: "GET",
            url: "/dashboard/ajaxGetDataListofSalesRep", 
            data: {},
            success: function (data) {
                var getData = data;                
                var parsed = '';               
                try{
                  parsed = JSON.parse(data);                  
                }           
                catch(e)
                {                   
                  return false;                  
                }
                
                window.pickupAgentlist = parsed;
                var setHtml = '';
                for (var i = 0; i < parsed.length; i++) {
                
                var getUserimage = parsed[i].image;
                if(getUserimage == null)
                {
                  getUserimage = 'sampleUser.png';
                }
                
                setHtml +="<div class='full gap-bottom' userid='"+parsed[i].user_id+"' username='"+parsed[i].name+"'>";
                setHtml += "<img src='/profile_image/"+getUserimage+"' />";      
                setHtml += "<a class=''>"+ parsed[i].name +"</a>";
                setHtml +="</div>";
              }
              window.pickAgentresult2 = setHtml
            }

        });

  }
  pickUpAgents ();


  $(document).on('keyup', '.addBookingPopup #customerRepSelect input', function () {
    var getValue = $(this).val();
    getValue = getValue.toLowerCase();
    var getLength = getValue.length;
    var arr = [];
    var arr2 = [];
    if(getLength > 0)
    {
      var parsed = window.list;

      for (var i = 0; i < parsed.length; i++) {
          
          var result = parsed[i].LeadCustomerFullName;
          if(result != null)
          {
            var resultNew = result.toLowerCase();
            var result2 = parsed[i].Lead_id;
            if (resultNew.indexOf(getValue) > -1) {
             arr.push(result);
             arr2.push(result2);
            }
            else {}
          }
          
        }
    }
    var parsed2 = arr;
    var parsed3 = arr2;
    var setHtml = '';
      for (var i = 0; i < parsed2.length; i++) {
        setHtml += "<a class='full' leadId='"+ parsed3[i] +"'>"+ parsed2[i] +"</a>";
      }

      if(parsed2.length > 0)
      {
        $(".customerresult").html(setHtml);
      }
      else
      {
        $(".customerresult").html(' ');
      }
      
  });// End

  $(document).on('keyup', '.addBookingPopup #salesRepSelect input', function () {
      var getValue = $(this).val();
      getValue = getValue.toLowerCase();
      var getLength = getValue.length;
      if(getLength == 0)
      {
        $(".pickAgentresult").addClass('hide');
        //$(".pickAgentresult2").removeClass('hide');
        return false;
      }
      else
      {
        //$(".pickAgentresult2").addClass('hide');
        $(".pickAgentresult").removeClass('hide');
      }
      var arr = [];
      if(getLength > 0)
      {
        var parsed = pickupAgentlist;

        for (var i = 0; i < parsed.length; i++) {
            
            var result = parsed[i].name;
            var resultNew = result.toLowerCase();
             if (resultNew.indexOf(getValue) > -1) {
               arr.push(parsed[i]);
             }
             else {}
           }
      }
      var parsed2 = arr;
      var setHtml = '';
        for (var i = 0; i < parsed2.length; i++) {
          //[5].image
          var getUserimage = parsed2[i].image;
          if(getUserimage == null)
          {
            getUserimage = 'sampleUser.png';
          }

          setHtml +="<div class='full gap-bottom' userid='"+parsed2[i].user_id+"' username='"+parsed2[i].name+"'>";
          setHtml += "<img src='/profile_image/"+getUserimage+"' />";      
          setHtml += "<a class=''>"+ parsed2[i].name +"</a>";
          setHtml +="</div>";
        }

        if(parsed2.length > 0)
        {
          $(".pickAgentresult").html(setHtml);
        }
        else
        {
          $(".pickAgentresult").html(' ');
        }
        
    });// End


  // Select Pick up Assignee on click
  $(document).on('click', '.pickAgentresult div, .pickAgentresult2 div', function () { 
    $('.saveBookingError').addClass('hide');
    var $el = $(this);
    var value = $el.attr('username');
    var Id = $el.attr('userid');
    var startDate = $('.addBookingLink.thisClicked').closest('.daysContent').attr('fulldate');

    //-----------

      var leaveData =  {start_date : startDate , assign_UserId : Id}


      $.ajax({
        type: "GET",
        url: "/dashboard/ajaxCheckUserIsOnLeave",
        data: leaveData, 
        success: function (data) {
          
          var parsed = '';          
          try{                           
            parsed = JSON.parse(data);              
          }                 
          catch(e)                
          {                  
            return false;                  
          }
          
          var count = 0;    
          if (parsed != null)
          {
            $.each(parsed, function(key, value){
              count++
            });
          }
          var setMessage = value + ' is on leave!';

          if(count > 0)
          {
            //$('.showMessage div').html(setMessage);
            //$('.showMessage').addClass('topShow');
            //  setTimeout(function(){ 
            //    $('.showMessage').removeClass('topShow');
            //}, 5000);

            $('.dialogeBox.leaveCheck .boxmessage').html(setMessage);
            $('.dialogeBox.leaveCheck').removeClass('hide');
                setTimeout(function(){ 
                  $('.dialogeBox.leaveCheck').addClass('hide');
            }, 3000);
            return false; 
          }
          else
          {
            $el.closest('.borderBottom').find('.salesRepName').html(value).attr('value',value);
            $el.closest('.borderBottom').find('.salesRepName').attr('userId',Id);
            $el.closest('.borderBottom').find('.subheading').removeClass('hide');
            $el.closest('.borderBottom').find('.newbookingdropdown').addClass('hide');
            //$('#salesRepSelect').addClass('hide');
          }
        }
      }); 

    //-----------
    
  });// End


    // Create Lead from Existing email
    $(document).on('click', '.yesCreateEmailCustomer', function () {

      $('.dialogeBox.emailCheck').addClass('hide');
      $('.thisLeadId').attr('leadid','');
      $('.basicInfo').html(window.getBasicInfo);
      $('.additional-details').html(window.getAdditionalInfo);
      $('.dialogeBox.creatingCustomer').addClass('hide');
      //$('.ShowPopup').removeClass('topShow');
      var customer = window.createNewCustomerFromEmail;

      $("#customerId").attr('customerId',customer.Customer_id);

      $("#searchResults").html(' ');
      $(customer.CustomerTitle == null)
      {customer.CustomerTitle = 'Mr'}
      $('.dropdown.title .dropdownOptions li a[value="'+customer.CustomerTitle+'"]').trigger('click');               // title gender   
      $('.basicInfo .firstname').val(customer.CustomerFirst_name);                                                   // first name   
      $('.basicInfo .lastname').val(customer.CustomerLast_name);                                                     // last name

      if(customer.CustomerMobile == null)                                                                            // phone
      {
      customer.CustomerMobile ='';
      }
      else
      {
      $('.basicInfo .phonenumber').attr('readOnly', 'readOnly');

      }
      $('.basicInfo .phonenumber').val(customer.CustomerMobile);                                                             

      if(customer.CustomerEmail == null)                                                                             // email
      { customer.CustomerEmail = ''; }
      else
      { $('#email').attr('readOnly', 'readOnly'); }
                                                                                                                            
      $('#email').val(customer.CustomerEmail);                                                                       
      //$('#email').addClass('newcustomerLead'); 

      if(customer.CustomerAddress ==  null)
      {$('#fullAddress').val('');}
      else
      {$('#fullAddress').val(customer.CustomerAddress);} 
      if(customer.CustomerCountry_id == null)                                                                        // country
      {
      setTimeout(function(){ 
        $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val('Australia');
        $('#countryName').attr('value','Australia'); 
        $('.emailexists').addClass('green');
      }, 2000);
      }
      else
      {
      setTimeout(function(){ 
        $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val(customer.CustomerCountry_id);
        $('#countryName').attr('value',customer.CustomerCountry_id);
        $('.emailexists').addClass('green');
      }, 2000);

      }                                                                                                                         // address
                                                   
      $('.countryDiv .ui-state-default, .countryDiv .ui-autocomplete-input').val(customer.CustomerCountry_id);         // country
      $('.stateDiv .dropdown.State .dropdownOptions li a[stateid="'+customer.CustomerState_id+'"]').trigger('click');  // state
      if(customer.CustomerCountry_id == 'Australia' ||  customer.CustomerCountry_id == null)                 // state
      {
      $('.stateDiv').removeClass('hide');
      }
      else
      {
      $('.stateDiv').addClass('hide');
      }
      $('.emailexists').addClass('opacity0').removeClass('green');
      $('.redCross, .redGreen').addClass('hide');
      $('.basicInfo .requiredError, .emailDiv .requiredError, .firstError, .emailexists, .emailDiv .error ').addClass('opacity0');
      $('.formfields input').each( function () {
        if($(this).val().length > 0)
        {
          $(this).prev('span').slideDown(150);
        }    
      });// End
      $('#email').addClass('popuplatedemail');
      //$('#phonenumber').focusin();
      //$('#phonenumber').focusout();


    });// End

    // Cancel Create Lead from Existing email popup
    $(document).on('click', '.NoCancelEmailCustomer', function () {
        $('.dialogeBox.emailCheck').addClass('hide');
    });// End


    
    function onFocusOuts() {
        var ifemailPopulated = $("#email").attr('readonly');
        if(ifemailPopulated == 'readonly')
        { return false}
        $('#email').next().addClass('opacity0').next().next('.requiredError').addClass('opacity0');

        var getemail = $('#email').val();
        var popuplatedemail = $('#email').hasClass('popuplatedemail');
        var newcustomerLead = $('#email').hasClass('newcustomerLead');
        if(popuplatedemail == true)
        { 
          var getLeadId =  $('#email').attr('leadId');  
        }
        else
        { 
          var getLeadId =  $('.thisLeadId').attr('leadid');  
        }

        var getValue = $('#email').val().length;
        
        if ($.trim(getemail).length == 0) {
            $('.emailexists').addClass('opacity0');
            $('#email').next('label').next('label').addClass('opacity0');
            $('.redCross, .redGreen').addClass('hide');
        }
        else if (isValidEmailAddress(getemail)) {
            $('#email').next('label').next('label').addClass('opacity0');
             $.ajax({
              type: "GET",
              url: "/dashboard/ajaxGetCustomerAgainstEmail",
              data: {email : getemail},
              success: function (data) 
              {
                //$('.showloading').show();
                var parsed = '';          
                try{                           
                  parsed = JSON.parse(data);              
                }                 
                catch(e)                
                {                  
                  return false;                  
                }

                if(newcustomerLead == true || popuplatedemail == true)
                {
                    $('.redCross').addClass('hide');
                    $('.emailexists').html('Email Available!').addClass('green');
                    $('.emailexists').addClass('opacity0');
                    window.emailexists = false;
                }
                else if(parsed.IsEmailExists == 1)
                {
                  $('.topBar').trigger('click');
                  $('.redCross').removeClass('hide');
                  $('.redGreen').addClass('hide');
                  $('.emailexists').html('Email Already Exists!').removeClass('opacity0').removeClass('green');
                  $('.requiredError').addClass('opacity0');
                  if(parsed.IsCustomerData ==  1)
                  {
                    $('.dialogeBox.emailCheck').removeClass('hide');
                    var setHtml = 'This email address belongs to an existing customer ( '+ parsed.Customer.CustomerFirst_name+ ' ' + parsed.Customer.CustomerLast_name +' ). Would you like to create a new lead for this customer?';
                    window.createNewCustomerFromEmail = parsed.Customer;
                    $('.dialogeBox.emailCheck .boxmessage').html(setHtml);
                  }
                  
                }
                else
                {
                  $('.redCross').addClass('hide');
                  $('.redGreen').removeClass('hide');
                  $('.emailexists').html('Email Available!').removeClass('opacity0').addClass('green');
                  $('.requiredError').addClass('opacity0');

                }        
              
              }
            }); 

        }
        else {
            $('.redCross, .redGreen').addClass('hide');
            $('.emailexists, .requiredError').addClass('opacity0');
            $('#email').next('label').next('label').removeClass('opacity0');
            
        }
    }


    function onFocusOutsPhone() {
       $('#email').next().addClass('opacity0').next().next('.requiredError').addClass('opacity0');

        var getPhone = $('#phonenumber').val();
        var popuplatedemail = $('#email').hasClass('popuplatedemail');
        var newcustomerLead = $('#email').hasClass('newcustomerLead');
        var getValue = $('#phonenumber').val().length;
        //$('.phonefield .firstError')
        //$('.phonefield .requiredError')

        if ($.trim(getPhone).length == 0) {
            $('.phonefield .firstError,.phonefield .requiredError,.phonefield .phoneexists').addClass('opacity0');
        }
        else if (validatePhone(getPhone)) {
            $('.phonefield .firstError,.phonefield .requiredError,.phonefield .phoneexists').addClass('opacity0');
             $.ajax({
              type: "POST",
              url: "/ajaxcheckDuplicateEmail",
              data: {checkfor: 'mobile' , value : getPhone},
              success: function (data) 
              {
                var parsed = '';          
                try
                {                           
                  parsed = JSON.parse(data);              
                }                 
                catch(e)                
                {                  
                  return false;                  
                }
                
                if(newcustomerLead == true || popuplatedemail == true)// Comes from Search
                {
                    $('#phonenumber').attr('passOn','true');
                }
                else if(parsed == 1) //Phone Exists
                {
                  $('#phonenumber').attr('passOn','false');
                  $('.phoneexists').removeClass('opacity0');
                }
                else //Phone Does Not Exists
                {
                  $('#phonenumber').attr('passOn','true');
                }        
              }
            });
        }
        else 
        {
            $('.phonefield .firstError,.phonefield .requiredError,.phonefield .phoneexists').addClass('opacity0');
            $('.phonefield .firstError').removeClass('opacity0');
            $('#phonenumber').attr('passOn','false');
            
        }
    }
    
   /*------------------------------------------------------------------*/
   /*------------------- Stop GetCountries Ajax List ----------------- */
   /*------------------------------------------------------------------*/
   

function getStates()  //  Get States
{
    $.ajax({
        type: "GET",
        url: "/dashboard/ajaxGetStateList",
        success: function (data) { 
            var parsed = '';          
            try{ parsed = JSON.parse(data); }                 
            catch(e)                
            { return false; }
            var check = parsed;
            var setHtml = ""
            for (var i = 0; i < parsed.length; i++) {
                setHtml +='<li><a href="javascript:;" stateId="'+parsed[i].id+'" value="'+parsed[i].state_code+'">'+parsed[i].state_code+'</a></li>';
              }
            $('.dropdown.State').find('ul.dropdownOptions').html(setHtml);
            window.getBasicInfo = $('.basicInfo').html();
            window.getNewLeadAll = $('.newLead').html();
        }
    });  
}


setTimeout(function(){ 
getStates();
    
}, 1000);

/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/

function getProducts() //  Get Countries
{
    $.ajax({
        type: "GET",
        url: "/dashboard/ajaxGetProductsList",
        success: function (data) { 
            var parsed = '';          
            try{ parsed = JSON.parse(data);  }                 
            catch(e)                
            { return false; }
            var check = parsed;
            var setHtml = ""
            for (var i = 0; i < parsed.length; i++) {
                setHtml +='<li><a href="javascript:;" productId="'+parsed[i].id+'" value="'+parsed[i].title+'" shortcode="'+parsed[i].title_shortcode+'">'+parsed[i].title+'</a></li>';
              }
            $('.dropdown.product').find('ul.dropdownOptions').html(setHtml);
            window.getAdditionalInfo = $('.additional-details').html();
            window.getNewLeadAll = $('.newLead').html();
        }
    });  
}

getProducts();

/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/

function howHeard() //  Get How Heard
{
    $.ajax({
        type: "GET",
        url: "/dashboard/ajaxGetHowHeardList",
        success: function (data) { 
            var parsed = '';          
            try{ parsed = JSON.parse(data);  }                 
            catch(e)                
            { return false; }
            var check = parsed;
            var setHtml = ""
            for (var i = 0; i < parsed.length; i++) 
            {
                if(parsed[i].id == 1)
                {
                  setHtml +='<li><a href="javascript:;" howHeardId="'+parsed[i].id+'" value="'+parsed[i].how_heard+'"><span class="ref-Img"><img class="pull-left" src=" /images/ic-google.png"></span> '+parsed[i].how_heard+'</a></li>';  
                }
                else if(parsed[i].id == 2)
                {
                  setHtml +='<li><a href="javascript:;" howHeardId="'+parsed[i].id+'" value="'+parsed[i].how_heard+'"><span class="ref-Img"><img class="pull-left" src=" /images/ic_wordMouth.png"></span> '+parsed[i].how_heard+'</a></li>';  
                }
                else if(parsed[i].id == 3)
                {
                  setHtml +='<li><a href="javascript:;" howHeardId="'+parsed[i].id+'" value="'+parsed[i].how_heard+'"><span class="ref-Img"><img class="pull-left" src=" /images/ic_facebook.png"></span> '+parsed[i].how_heard+'</a></li>';  
                }
                else if(parsed[i].id == 8)
                {
                  setHtml +='<li><a href="javascript:;" howHeardId="'+parsed[i].id+'" value="'+parsed[i].how_heard+'"><span class="ref-Img"><img class="pull-left" src=" /images/ic_insta.png"></span>'+parsed[i].how_heard+'</a></li>';  
                }
                else if(parsed[i].id == 11)
                {
                  setHtml +='<li><a href="javascript:;" howHeardId="'+parsed[i].id+'" value="'+parsed[i].how_heard+'"><span class="ref-Img"><img class="pull-left" src=" /images/ic_pClient.png"></span>'+parsed[i].how_heard+'</a></li>';  
                }
                else if(parsed[i].id == 12)
                {
                  setHtml +='<li><a href="javascript:;" howHeardId="'+parsed[i].id+'" value="'+parsed[i].how_heard+'"><span class="ref-Img"><img class="pull-left" src=" /images/ic_walkIn.png"></span> '+parsed[i].how_heard+'</a></li>';  
                }
                

                
            }
            setHtml +='<li><a href="javascript:;" howHeardId="10" value="Other"><span class="ref-Img"><img class="pull-left" src=" /images/ic_other.png"></span>Other</a></li>';  
            $('.dropdown.referral').find('ul.dropdownOptions').html(setHtml);
            window.getAdditionalInfo = $('.additional-details').html();
            window.getNewLeadAll = $('.newLead').html();
        }
    });  
}

howHeard();

/*------------------------------------------------------------------*/
/*------------------------------------------------------------------*/

setTimeout(function(){ 

    window.GetAdditionalDetails = $('.additional-details').html();
    window.getNewLeadAll = $('.newLead').html();
    window.getBasicInfo = $('.basicInfo').html();
    window.getAdditionalInfo = $('.additional-details').html();
    
}, 4000);





