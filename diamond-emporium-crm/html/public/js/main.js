// JavaScript Document

$(document).ready(function () {
// Leads multi select calendar


/*------------------------------------------------------------------*/
/*---------------------Start Onload Function------------------------ */
/*------------------------------------------------------------------*/
    
    
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

                //console.log(arr);
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
                  if(arr[i][0].status == 0)
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
                $('.assignToDiv a.selected-text span').html('Assign to');
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
        $('.user-list').addClass('hide');
        $('.' + getValue).removeClass('hide');

      var userStatusValue = getValue.replace('menu' , '');
      if(userStatusValue == 'Lunch' && getTimeValue == undefined)
      {
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
      /*-----------------------------------------------------*/
      //User Update Ajax Call
       $.ajax({
        type: "POST",
        url: "/ajaxuserstatusupdate",
        data: {status: userStatusValue},
        success: function (data) {}
        });    
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
            $('.userDropdown div[value="menuLunch"]').trigger('click');
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
            $('.userDropdown div[value="menuLunch"]').trigger('click');
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
            $('.userDropdown div[value="menuLunch"]').trigger('click');
            $('.userDropdown').removeClass('loadingContent');
            $('.user-dp-Dropdown').removeClass('hide');
            $('.menuLunch .displayPicture img').attr('src','/images/lunch145.svg');
           
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
            $('.userDropdown div[value="menuLunch"]').trigger('click');
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

   //Set Interval of 30 second on function of getUserStatus()

   setInterval(function(){
       // getUserStatus();  // this will run after every 5 seconds
   }, 30000);

/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    $(document).on('focus', '.formfields input', function () { 
      //$('.basicInfo span').slideDown(300);
      //$('.add-address, .next-saveDiv').slideDown(300);  
      
    });// End

    // Showing top headings
    $(document).on('keyup', '.formfields input', function () {
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
        var getName = $(this).val();
        if ($.trim(getName).length == 0) {
            $(this).next('label').addClass('opacity0');
            $(this).removeClass('hasError');
        }
        else if (isValidNames(getName)) {
            $(this).next('label').addClass('opacity0');
            $(this).removeClass('hasError');
            validateBasicInfo();
        }
        else {
            $(this).next('label').removeClass('opacity0');
            $(this).addClass('hasError');
        }
    });// End

/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    // Validating Email and opening next screen buttons
    $(document).on('keyup', '.basicInfo input.checkEmailCount', function () {
        var getFirstNameValue = $('.firstname').val().length;
        var getLastNameValue = $('.lastname').val().length;
        var getPhoneValue = $('.phonenumber').val().length;
        var getValue = $(this).val().length;
        var getemail = $(this).val();
        if ($.trim(getemail).length == 0) {
            $(this).next('label').addClass('opacity0');
        }
        else if (isValidEmailAddress(getemail)) {
            $(this).next('label').addClass('opacity0');
            validateBasicInfo();
        }
        else {
            $(this).next('label').removeClass('opacity0');
        }

    });// End

/* ----------------------------------------------------*/
/* ----------------------------------------------------*/
/* ----------------------------------------------------*/

    //Validating Phone Number
    $(document).on('keyup', '.basicInfo input.phonenumber', function () {
        var getphone = $(this).val();
        var getphoneLength = $(this).val().length;
        if(getphone == 0)
        {$(this).next('label').addClass('opacity0');}
        else if(!validatePhone(getphone)) { 
            $(this).next('label').removeClass('opacity0');
        }
        else
            {
                $(this).next('label').addClass('opacity0');
                validateBasicInfo();
            }
    });// End

/* ----------------------------------------------------*/
    
    // Show additional details container

    $(document).on('click', '.add-addressClick', function () {
        $('.add-address').slideUp();
        $('.addressContainer').slideDown(300);
    });// End

    // Show additional details container

    $(document).on('click', '.btn-nextDetails', function () {
      $('.additional-details').slideDown(300);  
      $('.btn-nextDetails').addClass('hide');
      $('.btn-saveDetails, .btn-bookNow').removeClass('hide');
      $('.next-saveDiv').addClass('one-half-pad-top');
      //$('.add-address').slideUp();
      $('.newLead').removeClass('opened');
      $('#calendar').fullCalendar("destroy");
      $('.btn-cancel').removeClass('gap-right').addClass('triple-gap-right');
      $('.newLead').removeClass('opened');
      $('.additional-details').addClass('opened');
      //loadCalendar1();
    });// End
    
    //$('.basicInfo').keypress(function(e) {
    //  if(e.keyCode == 13) {
    //    if($('.newLead').hasClass('opened'))
    //      {
    //          $('.additional-details').slideDown(300);  
     //         $('.btn-nextDetails').addClass('hide');
    //          $('.btn-saveDetails, .btn-bookNow').removeClass('hide');
    //          $('.next-saveDiv').addClass('one-half-pad-top');
    //          //$('.add-address').slideUp();
    //          $('.newLead').removeClass('opened');
    //      }
    //  }
    //});

/* ----------------------------------------------------*/

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
    
    // Time selection for Calendar 1

    var calendar1Events13 = [
                {
                        title: '8:00 - 9:00',
                        start: '2017-09-01',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-01',
                        className: 'calendarRed'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-01',
                        className: 'calendarRed'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-01',
                        className: 'calendarRed'
                    },
                    //
                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-02',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-02',
                        className: 'calendarRed'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-02',
                        className: 'calendarRed'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-02',
                        className: 'calendarRed'
                    },
                    //
                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-04',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-04',
                        className: 'calendarRed'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-04',
                        className: 'calendarRed'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-04',
                        className: 'calendarOrange'
                    },
                    //

                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-05',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-05',
                        className: 'calendarGreen'
                    },
                    {
                        title: 'Appointment',
                        start: '2017-09-05',
                        className: 'appointment'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-05',
                        className: 'calendarOrange'
                    },
                    //
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    //
                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-08',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-08',
                        className: 'calendarGreen'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-08',
                        className: 'calendarGrey'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-08',
                        className: 'calendarOrange'
                    },
                    //
                    {
                        title: 'Annual leave',
                        start: '2017-09-16',
                        className: 'leave'
                    }
            ]

    $(document).on('click', '.timeSelection_Cl1 a', function () {
        $("#calendar").fullCalendar('removeEvents'); 
        $("#calendar").fullCalendar('addEventSource', calendar1Events);

        $('.timeSelection_Cl1 a').removeClass('active');
        $(this).addClass('active');

        //$('#calendar').fullCalendar("destroy");
        //loadCalendar1();
    });

    $(document).on('click', '.durationSelection_Cl1 a', function () {
        $("#calendar").fullCalendar('removeEvents'); 
        $("#calendar").fullCalendar('addEventSource', calendar1Events);

        $('.durationSelection_Cl1 a').removeClass('active');
        $(this).addClass('active');
        
        //$('#calendar').fullCalendar("destroy");
        //loadCalendar1();
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


    $(document).on('click', '.durationSelection a, .timeSelection a, .daySelection a, .weekSelection a', function () {
        if($('.durationSelection a').hasClass('active') && $('.timeSelection a').hasClass('active'))
            {
                suggestedDate();
            }
    });


    function suggestedDate() {
        
        var getAssigneeId = window.selectedAssigneeId;
        loadingCalendar2();
        var getDay = $('.daySelection a').filter('.active').attr('value');
        var getDate = '';
        var getFullDate = '';
        var getOnlyDate = '';
        var getWeek = $('.weekSelection a').filter('.active').attr('value');
        if(getWeek == "one")
        {
            getDate = $('.daySelection a').filter('.active').attr('data-date1');
            getFullDate = $('.daySelection a').filter('.active').attr('data-fullDate1');
            getOnlyDate = $('.daySelection a').filter('.active').attr('only-date1');
        }
        else if(getWeek == "two")
        {
            getDate = $('.daySelection a').filter('.active').attr('data-date2');
            getFullDate = $('.daySelection a').filter('.active').attr('data-fullDate2');
            getOnlyDate = $('.daySelection a').filter('.active').attr('only-date2');
        }
        else
        {
            getDate = $('.daySelection a').filter('.active').attr('data-date3');
            getFullDate = $('.daySelection a').filter('.active').attr('data-fullDate3');
            getOnlyDate = $('.daySelection a').filter('.active').attr('only-date3');
        }
        var getAmPm = $('.timeSelection a').filter('.active').attr('value');
        

        var setDate = getDay + " " + getDate + " 8:00 - 9:00 " + getAmPm;
        
        
        //$('.suggestedDate, #bookingDate').html(setDate);
        $('.suggestedDate').html('');
        $('.questionView').removeClass('hide');
        $('.btn-saveBooking').addClass('saveNow');
        
        $('#calendar2').fullCalendar("destroy");
        window.getFullDate = getFullDate;

        $('#bookingDate').attr('time', "8:00 - 9:00");
        $('#bookingDate').attr('timeZone', getAmPm);
        $('#bookingDate').attr('date', getFullDate); 
        $('.coverAreaBooking').removeClass('hide');
        loadQuestionViewcalnder(getDay, getFullDate, getOnlyDate, getAssigneeId, getAmPm);
    }

    function suggestedDateFromCalendar(getTime, getSelectedDate, getFullDate) {
        var getDay = $('.daySelection a').filter('.active').attr('value');
        var getAmPm = $('.timeSelection a').filter('.active').attr('value');
        var setDate = getSelectedDate + " " + getTime +" "+ getAmPm;
        
        //var time = getTime +" "+ getAmPm;
        //$('#bookingDate').attr('time', time);
        //$('#bookingDate').attr('date', getSelectedDate);

        $('#bookingDate').attr('time', getTime);
        $('#bookingDate').attr('timeZone', getAmPm);
        $('#bookingDate').attr('date', getFullDate);

        $('.suggestedDate, #bookingDate').html(setDate);
    }
    
/*--------------------------------------------------*/
    // Validation

    function validation()
    {
        var getProduct = $('#productDropdown').closest('a.selected-text').attr('value');
        var getReferral = $('#referralDropdown').closest('a.selected-text').attr('value');
        var getBudget = $('#budgetDropdown').closest('a.selected-text').attr('value');
        var getAgent = $('#assign_us_Dropdown').closest('a.selected-text').attr('value');
        var getState = $('#stateDropdown').closest('a.selected-text').attr('value');
        var getCity = $('#cityValue').val();
        var getPhone = $('#phonenumber').val();
        var getEmail = $('#email').val();
        if(getProduct == 'All' || getReferral == 'All' || getBudget == 'All' || getAgent == 'All' || getState == 'All' || getCity == 'All')
        {
            if(getProduct == 'All')
            { $('.producterror').removeClass('opacity0'); }
            
            if(getReferral == 'All')
            { $('.referralerror').removeClass('opacity0'); }

            if(getBudget == 'All')
            { $('.budgeterror').removeClass('opacity0'); }

            if(getAgent == 'All')
            { $('.agenterror').removeClass('opacity0'); }

            if(getState == 'All')
            { 
                $('.stateerror').removeClass('opacity0'); 
                $('.add-address').slideUp();
                $('.addressContainer').slideDown(300);
            }
            if(getCity == 'All')
            { 
                $('.cityerror').removeClass('opacity0'); 
                $('.add-address').slideUp();
                $('.addressContainer').slideDown(300);
            }
            if(getPhone == '')
            { 

            }
            if(getEmail == '')
            { 

            }

            return false;
        }
        else if($('.btn-bookNow').hasClass('canOpen'))
        {
            additionalDetailsMinimize();
            $('.icMinimize').addClass('hide');
            $('.icExpand').removeClass('hide');
            $('.next-saveDiv').addClass('hide');
            $('.bookNowDiv').removeClass('hide');
        }

    }

    // Book Now Open
    $(document).on('click', '.btn-bookNow', function () {
        validation();
    });

    // Book Now Open
    $(document).on('click', '.btn-bookNow.canOpen', function () {
        validation();

        //additionalDetailsMinimize();
        //$('.icMinimize').addClass('hide');
        //$('.icExpand').removeClass('hide');
        //$('.next-saveDiv').addClass('hide');
        //$('.bookNowDiv').removeClass('hide');
        //$(this).removeClass('open').addClass('close');
        //$('.bookingDiv').css('height','auto');   
    });

    /*-------------------------------------------------*/

    // Save Booking     
    $(document).on('click', '.btn-saveBooking.saveNow', function () {
        
        var checkBookingDate = $('#bookingDate').hasClass('nowCanSave');
        var checkBookingValue = $('.suggestedDate').html();

        if(checkBookingDate == false || checkBookingValue == "")
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
        $('.bookNowDiv').addClass('hide');
        additionalDetailsExpand();
        $('.savedBooking').addClass('hide');
        $('.btn-bookNow').removeClass('hide');
    });

/*------------------------------------------------*/

    // Book Now Calendar Close
    $(document).on('click', '.cancelBookedbooking', function () {
        $('.btn-bookNow').removeClass('hide');
        cancelBookedBookingAction();
    });

    /*------------------------------------------------*/

    // Book Now Calendar Close
    $(document).on('click', '.editBooking', function () {
        $('.next-saveDiv').addClass('hide');
        $('.bookNowDiv').removeClass('hide');
        additionalDetailsMinimize();
        $('.savedBooking').removeClass('hide');
        $('.btn-bookNow').addClass('hide');
        $('.bookingViewIcon').removeClass('hide');
        $('.bookingHeading').addClass('hide');
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


    // Select dropdown value
    
    $(document).on('click','ul.dropdownOptions li:not(.assignToDiv ul.dropdownOptions li)',function(){

        var el = $(this); 
        
        var getValue = $(this).find('a').attr('value');
        var getAsignee_Id = $(this).find('a').attr('id');
        el.closest('.dropdown').find('a.selected-text span').html(getValue);
        el.closest('.dropdown').find('a.selected-text').attr('value', getValue);
        el.closest('.dropdown').find('ul.dropdownOptions').slideToggle(150);
        el.closest('.dropdown').prev('span.text-top').slideDown(150);
        el.closest('.dropdown').next('select').find('option').attr('value',getValue);
        el.closest('.dropdown').next('label').addClass('opacity0');

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

        // Check if budget dropdown
        if(el.closest('.dropdown').hasClass('budget'))
        {
            GetUserBasedOnBudget(getValue);
            if($('.dropdownheightSet').hasClass('hide')) 
            { 
                $('.dropdownheightSet').hide().removeClass('hide'); 
            }
            $('.budgeterror').addClass('opacity0');
        }
        
        // Check if State dropdown
        if(el.closest('.dropdown').hasClass('State'))
        {   
            //$('.cityList li').addClass('hide');
            //$('.'+getValue).removeClass('hide');
            //$('.City .selected-text').attr('value','All');
            //$('#cityDropdown').html('City');
            $('.stateerror').addClass('opacity0');
        }

        // Check if City dropdown
        if(el.closest('.dropdown').hasClass('City'))
        {   
            $('.cityerror').addClass('opacity0');
        }

        // Check if Product dropdown
        if(el.closest('.dropdown').hasClass('product'))
        {   
            $('.producterror').addClass('opacity0');
        }

        // Check if Referral dropdown
        if(el.closest('.dropdown').hasClass('referral'))
        {   
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
        }
            
    });// End

    // Select dropdown for Assign To Div
    
    $(document).on('click','.assignToDiv ul.dropdownOptions li',function(){

            var el = $(this);

            $('ul.dropdownOptions li').removeClass('activeField');
            el.addClass('activeField');
            if($(this).hasClass('nextInline'))
            {
                var chek = "";
            }
            if(el.hasClass('nextInline'))
            { 
                var getId = $(this).find('a').attr('id');
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
                if($('.daySelection').hasClass('setForCalendar'))
                {
                  suggestedDate()
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


    $(document).on('click','.Additionaldrodown p' ,function(){
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

        var checkDefaultValue = $('.additioanlSelection a').attr('value');
        var checkSelectedValue = $('.AdditionaldrodownList p').filter('.activeReason').attr('value');

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
    });// End

    // Additional Agent other selection
    
    $(document).on('click','.Additionaldrodown  div.additioanlSelection',function(){
        var el = $(this);
        el.next('.AdditionaldrodownList').slideToggle(300);
    });// End

    // Submit Form

    function getValuesFromForm()
    {
        
        return {
          
            first_name : $("#first_name").val(),
            last_name : $("#last_name").val(),
            phone_number : $("#phonenumber").val(),
            email : $("#email").val(),
            Street : $("#street").val(),
            State : $("#stateDropdown").text(),
            City : $("#cityValue").val(),
            Zip : $("#Zip").val(),
            product : $("#productDropdown").text(),
            referral : $("#referralDropdown").text(),
            special_instructions : $("[name= 'special_instructions']").val(),
            budget : $("#budgetDropdown").text(),
            reference_product : $("#referrenceDropdown").val(),
            contact_method : $("#perferrefDropdown").text(),
            assign_to : $("#assign_us_Dropdown").text(),
            specify_requirements : $("#specify_requirements").val(),
            reson_skip_next_in_line : $("#skip_reason_dropdown").text(),
            booking_date : $("#bookingDate").attr("date"),
            booking_time : $("#bookingDate").attr("time"),
            booking_timezone : $("#bookingDate").attr("timezone"), 
            assign_id : $(".assignToDiv a.selected-text").attr("assigneid"),           
            //booking_room : $(".meetingRoomValue").attr('value')
            booking_room : '',
            lead_id : $('.thisLeadId').attr('leadId'),
            booking_duration: $('.durationSelection a').filter('.active').attr('value')

           
        };
        
    }
   
    $(document).on('click','#submitbutton', function (e) {
      
        var getProduct = $('#productDropdown').closest('a.selected-text').attr('value');
        var getReferral = $('#referralDropdown').closest('a.selected-text').attr('value');
        var getBudget = $('#budgetDropdown').closest('a.selected-text').attr('value');
        var getAgent = $('#assign_us_Dropdown').closest('a.selected-text').attr('value');
        var getState = $('#stateDropdown').closest('a.selected-text').attr('value');
        var getCity = $('#cityValue').val();
        var getPhone = $('#phonenumber').val();
        var getEmail = $('#email').val();
        var checkBookingDate = $('#bookingDate').hasClass('nowCanSave');

        if(getProduct == 'All' || getReferral == 'All' || getBudget == 'All' || getAgent == 'All' || getState == 'All' || getCity == 'All')
        {
            if(getProduct == 'All')
            { $('.producterror').removeClass('opacity0'); }
            
            if(getReferral == 'All')
            { $('.referralerror').removeClass('opacity0'); }

            if(getBudget == 'All')
            { $('.budgeterror').removeClass('opacity0'); }

            if(getAgent == 'All')
            { $('.agenterror').removeClass('opacity0'); }

            if(getState == 'All')
            { 
                $('.stateerror').removeClass('opacity0'); 
                $('.add-address').slideUp();
                $('.addressContainer').slideDown(300);
            }
            if(getCity == 'All')
            { 
                $('.cityerror').removeClass('opacity0'); 
                $('.add-address').slideUp();
                $('.addressContainer').slideDown(300);
            }
            if(getPhone == '')
            { 

            }
            if(getEmail == '')
            { 

            }

            return false;
        }
        if(checkBookingDate == false)
        {
            showBookingError();
            return false
        }

    
      var data = getValuesFromForm();
      //alert("Successfully triggered");
      //Ajax Call
        $.ajax({
        type: "POST",
        url: "/dashboard/ajaxAddDashboard",
        data: data, 
        success: function (data) {
            $('.rings a').removeClass('active');
            $('.rings a:last-child').addClass('active');
            loadLeads();
        }
      });    
      //End Ajax Call

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

      return false;
      //$("#dashboard").submit();

    });

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
          $('.next-saveDiv').addClass('one-half-pad-top');
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
          $('#submitbutton').trigger('click');
          return e.which !== 13;
        }
      }
    });

    // Calcelling form submission on enter press
    $('form input').on('keypress', function(e) {
          return e.which !== 13;
    });
    
    $("form").keypress(function(e) {
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

    // Full Calendar 1
        function loadCalendar1() {
            var date = new Date();
            var getTime = $('.timeSelection_Cl1 a').filter('.active').attr('value');
            var getDuration = $('.durationSelection_Cl1 a').filter('.active').attr('value');
            var calendar1Events = [
                {
                        title: '8:00 - 9:00',
                        start: '2017-09-01',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-01',
                        className: 'calendarGreen'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-01',
                        className: 'calendarGrey'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-01',
                        className: 'calendarOrange'
                    },
                    //
                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-02',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-02',
                        className: 'calendarGreen'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-02',
                        className: 'calendarGrey'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-02',
                        className: 'calendarOrange'
                    },
                    //
                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-04',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-04',
                        className: 'calendarGreen'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-04',
                        className: 'calendarGrey'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-04',
                        className: 'calendarOrange'
                    },
                    //

                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-05',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-05',
                        className: 'calendarGreen'
                    },
                    {
                        title: 'Appointment',
                        start: '2017-09-05',
                        className: 'appointment'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-05',
                        className: 'calendarOrange'
                    },
                    //
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    {
                        title: 'Annual leave',
                        start: '2017-09-06',
                        className: 'leave'
                    },
                    //
                    {
                        title: '8:00 - 9:00',
                        start: '2017-09-08',
                        className: 'calendarRed'
                    },
                    {
                        title: '9:00 - 10:00',
                        start: '2017-09-08',
                        className: 'calendarGreen'
                    },
                    {
                        title: '10:00 - 11:00',
                        start: '2017-09-08',
                        className: 'calendarGrey'
                    },
                    {
                        title: '11:00 - 12:00',
                        start: '2017-09-08',
                        className: 'calendarOrange'
                    },
                    //
                    {
                        title: 'Annual leave',
                        start: '2017-09-16',
                        className: 'leave'
                    }
            ]
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month'
                },
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                hiddenDays: [0],
                dayNamesShort: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                events: calendar1Events,
                eventOrder: "eventProperty",
                dayClick: function(date, jsEvent, view) {
                    
                },
                eventRender: function(event, element, view){
                },
                eventClick: function(calEvent, jsEvent, view) {
                  
                  if(calEvent.className == 'leave' || calEvent.className == 'appointment')
                  {}
                  else
                  {
                    $('#calendar table table tbody td a').removeClass('active');
                    $(this).addClass('active');
                  }
                  
                  if(calEvent.className == 'leave' || calEvent.className == 'appointment')
                  {
                    $('.booking-date, .booking-hour, .booking-assigned').attr('value', '');
                    $('.booking-date, .booking-hour, .booking-assigned').val('');
                  }
                  else
                  {
                    if(calEvent.title == '8:00 - 9:00')
                    {
                        $('.booking-hour').attr('value', '8:00 AM - 9:00 AM');
                        $('.booking-hour').val('8:00 AM - 9:00 AM');
                        $('#bookingDate').attr('timeZone', 'AM');
                    }
                    else if(calEvent.title == '9:00 - 10:00')
                    {
                        $('.booking-hour').attr('value', '9:00 AM - 10:00 AM');
                        $('.booking-hour').val('9:00 AM - 10:00 AM');
                        $('#bookingDate').attr('timeZone', 'AM');
                    }
                    else if(calEvent.title == '10:00 - 11:00')
                    {
                        $('.booking-hour').attr('value', '10:00 AM - 11:00 AM');
                        $('.booking-hour').val('10:00 AM - 11:00 AM');
                        $('#bookingDate').attr('timeZone', 'AM');
                    }
                    else
                    {
                        $('.booking-hour').attr('value', '11:00 AM - 12:00 AM');
                        $('.booking-hour').val('11:00 AM - 12:00 AM');
                        $('#bookingDate').attr('timeZone', 'AM');
                    }
                    $('.booking-date').attr('value', calEvent.start.format('MMMM D'));
                    $('.booking-date').val(calEvent.start.format('MMMM D'));

                    $('.booking-date').attr('fullDate', calEvent.start.format('YYYY-MM-DD'));


                    $('#bookingDate').attr('time', calEvent.title);
                    
                    $('#bookingDate').attr('date', calEvent.start.format('YYYY-MM-DD'));

                    $('.Confirmation').hide();
                    $('.saveBooking').slideDown(300);
                    //$('.booking-date').attr('value', calEvent.start.format('MMMM D'));

                  }

                }
            });
            setTimeout(function(){ $('.fc-day-number').removeAttr('data-goto'); }, 200);
        }
        //loadCalendar1();

        
        // show confirmation window
        $(document).on('click','.btnSaveBooking', function() {
            
            var getBookingDate = $('.booking-date').val();
            var getBookingHour = $('.booking-hour').val();
            var getBookingHour = $('.meetingRoom').attr('value');
            if(getBookingDate == '' || getBookingHour == '' || getBookingHour == "All")
            {}
            else
            {
                $('.Confirmation').slideDown(200);
                $('.saveBooking').hide();
            }
        });

        // Month right arrow click
        $(document).on('click','.fc-icon-left-single-arrow', function() {
            setTimeout(function(){ $('.fc-day-number').removeAttr('data-goto'); }, 200);
        });

        $(document).on('click','.fc-icon-right-single-arrow', function() {
            setTimeout(function(){ $('.fc-day-number').removeAttr('data-goto'); }, 200);
        });

        
        //

    // Full Calendar 2 Events
    var eventingss = [
            {
                title: '8:00 - 9:00',
                start: '2017-10-07',
                className: 'green'
            },
            {
                title: '9:00 - 10:00',
                start: '2017-10-07',
                className: 'green'
            }, 
            {
                title: '10:00 - 11:00',
                start: '2017-10-07',
                className: 'green'
            },
            {
                title: '11:00 - 12:00',
                start: '2017-10-07',
                className: 'orange'
            },
            {
                title: '12:00 - 1:00',
                start: '2017-10-07',
                className: 'orange'
            },
            //
            {
                title: '8:00 - 9:00',
                start: '2017-10-14',
                className: 'red'
            },
            {
                title: '9:00 - 10:00',
                start: '2017-10-14',
                className: 'green'
            },
            {
                title: '10:00 - 11:00',
                start: '2017-10-14',
                className: 'green'
            },
            {
                title: '11:00 - 12:00',
                start: '2017-10-14',
                className: 'orange'
            },
            {
                title: '12:00 - 1:00',
                start: '2017-10-14',
                className: 'orange'
            },
            //
            {
                title: '8:00 - 9:00',
                start: '2017-10-21',
                className: 'red'
            },
            {
                title: '9:00 - 10:00',
                start: '2017-10-21',
                className: 'green'
            },
            {
                title: '10:00 - 11:00',
                start: '2017-10-21',
                className: 'green'
            },
            {
                title: '11:00 - 12:00',
                start: '2017-10-21',
                className: 'orange'
            },
            {
                title: '12:00 - 1:00',
                start: '2017-10-21',
                className: 'orange'
            },
            //
            {
                title: '8:00 - 9:00',
                start: '2017-10-28',
                className: 'red'
            },
            {
                title: '9:00 - 10:00',
                start: '2017-10-28',
                className: 'green'
            },
            {
                title: 'Not available',
                start: '2017-10-28',
                className: 'grey'
            },
            {
                title: '11:00 - 12:00',
                start: '2017-10-28',
                className: 'orange'
            },
            {
                title: '12:00 - 1:00',
                start: '2017-10-28',
                className: 'orange'
            },
            //
    ]
    

    //loadAgentCalendar(35 , '2017-10-16');
    
    
    
/* ------------------ Start Edit Detail ------------------------*/
/* ------------------ Start Edit Detail ------------------------*/
/* ------------------ Start Edit Detail ------------------------*/
    
    $(document).on('click','.editDetails:not(.disabled)', function (e) {
    showMainLoading();  
    var getLeadId = $(this).attr('lead-id');    
    
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

              var html = "";
             
              //  " [{"id":"16","first_name":"Alfateh","last_name":"Test","phone_number":"090078601","email":"alfa@gmail.com","Street":"3rd floor, CCA-1, Phase-5, DHA, lahore","State":"NSW","City":"Lahore","Zip":"54000","product":"Wedding Band","referral":"Google","special_instructions":"3rd floor, CCA-1, Phase-5, DHA, lahore","budget":"$5-10k","reference_product":"","contact_method":"Phone call","assign_to":"test test","assign_to_UserId":"53","reson_skip_next_in_line":"Reason ","lead_status":"Closed","specify_requirements":"3rd floor, CCA-1, Phase-5, DHA","lead_owner":"35","create_date":"2018-02-14 05:00:30","lead_close_date":"2018-1-2 00:00:00","booking_date":"2018-02-05","booking_time":"8:00 - 9:00","booking_timezone":"AM","booking_room":"1"}]"
              
              //Setting Lead Id
              
              $('.thisLeadId').attr('leadId',parsed[0].id);
              
              // Basic Info Fields
              $('.basicInfo .firstname').val(parsed[0].first_name);
              $('.basicInfo .lastname').val(parsed[0].last_name);
              $('.basicInfo .phonenumber').val(parsed[0].phone_number);
              $('.basicInfo .email').val(parsed[0].email);
              // Address Fields
              $('.basicInfo .street').val(parsed[0].Street);
              $('.basicInfo .dropdown.State .selected-text').attr('value',parsed[0].State);
              $('.basicInfo .dropdown.State .selected-text span').html(parsed[0].State);
              $('.basicInfo .cityValue').val(parsed[0].City);
              $('.basicInfo .Zip').val(parsed[0].Zip);
              // Additional Detail Fields
              $('.additional-details .dropdown.product .selected-text').attr('value',parsed[0].product);
              $('.additional-details .dropdown.product .selected-text span').html(parsed[0].product);
              $('.additional-details .dropdown.referral .selected-text').attr('value',parsed[0].referral);
              $('.additional-details .dropdown.referral .selected-text span').html(parsed[0].referral);
              $('.additional-details .instructions').val(parsed[0].special_instructions);
              $('.additional-details .ReferenceProduct').val(parsed[0].reference_product);
              $('.additional-details .dropdown.budget .dropdownOptions li a[value="'+parsed[0].budget+'"]').click();
              $('.additional-details .dropdown.budget .dropdownOptions').hide();
              $('.additional-details .preferredMethod .selected-text').attr('value',parsed[0].contact_method);
              $('.additional-details .preferredMethod .selected-text span').html(parsed[0].contact_method);
              $('.additional-details .requirements').val(parsed[0].specify_requirements);
              
              setTimeout(function(){ 
                  $('.additional-details .dropdown.assignToDiv .dropdownOptions li a[id="'+parsed[0].assign_to_UserId+'"]').click();
                  $('.additional-details .dropdown.assignToDiv .dropdownOptions').hide();
                  $('.additional-details .dropdown.assignToDiv .dropdownOptions .btn-skip').click();

                  if(parsed[0].reson_skip_next_in_line != "")
                  {
                      debugger;
                      $('.otherReasonDiv').removeClass('hide');
                      $('.otherReasonDiv a.selected-text').attr('value',parsed[0].reson_skip_next_in_line);
                      $('.otherReasonDiv a.selected-text span').html(parsed[0].reson_skip_next_in_line);
                  }
                  
                  // Booking Calendar
                  
                  var getAmPm = parsed[0].booking_timezone;
                  var getFullDate = parsed[0].booking_date;
                  var getAssigneeId = parsed[0].assign_to_UserId;
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
                  
                  
                    
                  $('.suggestedDate').html('');
                  $('.questionView').removeClass('hide');
                  $('.btn-saveBooking').addClass('saveNow');
                  // destroy calendar
                  $('#calendar2').fullCalendar("destroy");

                  $('#bookingDate').attr('time', "8:00 - 9:00");
                  $('#bookingDate').attr('timeZone', getAmPm);
                  $('#bookingDate').attr('date', getFullDate); 
                  $('.coverAreaBooking').removeClass('hide');
                  $('.timeSelection a[value="'+getAmPm+'"]').addClass('active'); // Setting am pm active class
                  $('.daySelection a[value="'+getDay+'"]').addClass('active'); // Setting day active class
                  $('.durationSelection a[value="'+parsed[0].booking_duration+'"]').addClass('active'); // Setting duration active class
                                    
                  loadQuestionViewcalnder(getDay, getFullDate, getOnlyDate, getAssigneeId, getAmPm); // SLoading Calendar

                  $('#bookingDate').attr('timezone', getAmPm);
                  $('#bookingDate').attr('date', getFullDate);
                  $('#bookingDate').attr('time', parsed[0].booking_time);
                  $('#bookingDate').addClass('nowCanSave');
                  
                  $('#bookingDate, .suggestedDate').html(getDay + ' ' + getOnlyDate + ' ' + getMonth + ' ' + parsed[0].booking_time + ' ' + getAmPm); // Setting booking date field
              }, 1000);
              
              
              
 
              
              $('.leadDeailContainer').addClass('hide');
              $('.newLead').removeClass('maxHeightHide');
              $('.dashboard-header').addClass('hide');
              $('.basicInfo div.relative span, .additional-details div.relative span:first-child').css('display','inline-block');
              $('.addressContainer, .additional-details').show();
              $('.selectWeek, .selectTime').removeClass('opacity0')
              $('.bookNowDiv').removeClass('hide'); 
              $('.btn-nextDetails').addClass('hide');
              $('.btn-saveDetails').removeClass('hide');
                
            }
        });            
});
/* ------------------ End Edit Detail ------------------------*/
/* ------------------ End Edit Detail ------------------------*/
/* ------------------ End Edit Detail ------------------------*/

/*------------------------------------------------------------------*/
/*--------------------- --Load Question View Calender Start ------- */
/*------------------------------------------------------------------*/


    function loadQuestionViewcalnder(getDay, getFullDate, getOnlyDate, getAssigneeId, getAmPm)
    { 

           var data =  {booking_date : getFullDate , day : getDay, assigneeId : getAssigneeId, booking_timezone : getAmPm}
           // Check if Timezone is AM or PM
          if(getAmPm == "AM")
          { 
            $('.PM-heading').addClass('hide'); 
            $('.AM-heading').removeClass('hide'); 
            $('#calendar2').addClass('amHeight');
            $('#calendar2').removeClass('pmHeight');
          }
          else
          { 
            $('.AM-heading').addClass('hide'); 
            $('.PM-heading').removeClass('hide'); 
            $('#calendar2').addClass('pmHeight');
            $('#calendar2').removeClass('amHeight');
          
          }
            // Get 
           $.ajax({
                
                type: "GET",
                url: "dashboard/ajaxGetDataForQuestionViewCalender",
                data: data,
                success: function (data) {
                  console.log(data);
                  // Convert Json into Array

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

                  console.log(arr);

                  var myArray = [];
                  var getArrayLength = arr[0].length;
                  var count = 0;

                  // if there is no data

                  if(arr.length == 1)
                  { 

                    for (var i = 0; i < getArrayLength; i++) {
                        var startDate = arr[0][i];              
                        addingArrayForElse(myArray, startDate, getAmPm)  
                    }// End For loop

                  }// End If statement
                  else
                  {
                    // Reverse Date Array 
                    arr[0].reverse();

                    var count1 = 0
                    
                    for (var i = 0; i < getArrayLength; i++) {
                      var count2 = 1
                      var startDate = arr[0][i];

                      if(arr[count2])
                      {
                        if( arr[0][i] == arr[count2].date ) 
                        {
                          if(arr[count2].status == null)
                          {
                            // 8 - 9 AM
                            addingArray(myArray, startDate, count2, arr, getAmPm)
                            continue;
                          }
                          else
                          {
                            addingLeaveArray(myArray, startDate, getAmPm)
                            continue;
                            console.log('annual leave');
                          }
                        }
                      }
                      count2++
                      if(arr[count2])
                      {
                        if(arr[0][i] == arr[count2].date)
                        {
                          if(arr[count2].status == null)
                          {
                            addingArray(myArray, startDate, count2, arr, getAmPm)
                            continue;
                          }
                          else
                          {
                            addingLeaveArray(myArray, startDate, getAmPm)
                            continue;
                            console.log('annual leave');
                          }
                        }
                        
                      }
                      count2++
                      if(arr[count2])
                      {
                        if(arr[0][i] == arr[count2].date)
                        {
                          if(arr[count2].status == null)
                          {
                            addingArray(myArray, startDate, count2, arr, getAmPm)
                            continue;
                          }
                          else
                          {
                            addingLeaveArray(myArray, startDate, getAmPm)
                            continue;
                            console.log('annual leave');
                          }
                        }
                        
                      }
                      count2++
                      if(arr[count2])
                      {
                        if(arr[0][i] == arr[count2].date)
                        {
                          if(arr[count2].status == null)
                          {
                            addingArray(myArray, startDate, count2, arr, getAmPm)
                            continue;
                          }
                          else
                          {
                            addingLeaveArray(myArray, startDate, getAmPm)
                            continue;
                            console.log('annual leave');
                          }
                        }
                        
                      }
                      count2++
                      if(arr[count2])
                      {
                        if(arr[0][i] == arr[count2].date)
                        {
                          if(arr[count2].status == null)
                          {
                            addingArray(myArray, startDate, count2, arr, getAmPm)
                            continue;
                          }
                          else
                          {
                            addingLeaveArray(myArray, startDate, getAmPm)
                            continue;
                            console.log('annual leave');
                          }
                        }
                        
                      }
                      else
                        {
                          addingArrayForElse(myArray, startDate, getAmPm)
                          continue;
                        }
                      }
                      
                  }// End Else Statement
                   
                  window.questionViewCalendar = myArray;
                  setTimeout(function(){ 
                    loadCalendar2(getDay, getFullDate, getOnlyDate);
                  }, 1000);
                   

            }
            
        });
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
                    showMessage(message)
                    return false;
                  }
                  if(calEvent.className == "onLeave")
                  { 
                    var getAssignee = $('.selectedAgent p.userName').text();
                    var message = getAssignee + " is on leave";
                    showMessage(message)
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

        //var getCheck = $('#calendar2 .fc-day-number[date=' + getOnlyDate + ']').closest('table').find('tbody tr:first-child .fc-event-container .fc-event').addClass('active');
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

        $('.suggestedDate').html('');
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
        $('.suggestedDate').html('');
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
}); // Ending Document ready

// Getting agents List

var myArray = [];
var AgentList = []; 
setTimeout(function(){ 

    window.GetAdditionalDetails = $('.additional-details').html();
    window.getNewLeadAll = $('.newLead').html();
    //$('.loadAgents ul.dropdownOptions').html(dropdownList);
    
}, 1000);


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
        var getEmailValue = $('.email').val().length;
        var getemail = $('.email').val();
        if(getFirstNameValue > 0 && getLastNameValue > 0 && getPhoneValue > 0 && getEmailValue > 0)
        {   

            if($('.firstname').hasClass('hasError') || $('.lastname').hasClass('hasError'))
            {}
            else if(validatePhone(getPhone) && isValidEmailAddress(getemail) ) { 
                if($('.additional-details').is(':hidden'))
                {
                    $('.add-address').slideDown(300);
                    $('.next-saveDiv').removeClass('hide');
                    $('.newLead').addClass('opened');
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
    //var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    var filter = /^[0-9 ]+$/
    if (filter.test(PhoneNumber)) {
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

//create new lead and leave

$(document).on('click','.newactions a', function (e) {

    $(".newactions a").removeClass('active');
    $(this).addClass('active');

    if($(this).hasClass('new-Lead'))
    {   
        $('.newLeaveContainer').hide();
        $('.leadsContainer').addClass('hide');
        $('.leavesContainer').addClass('hide');
        $('.dashboardContainer').addClass('hide');
        $('.newLead').removeClass('maxHeightHide');
        $('.dashboard-header').addClass('hide');
        $('.leadDeailContainer').addClass('hide');
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
});

//Function GetValues From the SaveLeads

    function getValuesFromLeaveForm()
    {
        
        return {
          
            date : $("#dateRange").val(),
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
        //showMainLoading();
        
        //AjaxCallSaveLeadsOnSaveClick--Start
         
         var model = getValuesFromLeaveForm();
        
         $.ajax({

          type: "POST",
          url: "/leave/ajaxSaveLeaves",
          data: model,
          success: function (data) {

          }
        });    
        
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

  var leaveClass = "";
  if(ReasonForLeave == "Sick leave")
  { leaveClass = "darkgreen"; }
  else if(ReasonForLeave == "Other")
  { leaveClass = "lightgreen"; }
  else
  { leaveClass = "orange"; }
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
          
        //var a = $('#calenderdisplay').multiDatesPicker('getDates');
        //jQuery("#calender1").setValue(a);
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

    //$(".leadListCalendar .monthCalendarDropdown").slideToggle(150);
    //$(".leadListCalendar .yearCalendarDropdown").slideUp(10);
});

/*---------------------------------------------*/
/*---------------------------------------------*/
/*---------------------------------------------*/

$(document).on('click','.calendarLeaveYear', function (e) {

    $(this).closest('.calendarContainer').find(".yearCalendarDropdown").slideToggle(150);
    $(this).closest('.calendarContainer').find(".monthCalendarDropdown").slideUp(10);
    //$(".leadListCalendar .yearCalendarDropdown").slideToggle(150);
    //$(".leadListCalendar .monthCalendarDropdown").slideUp(10);
});

// calendar month selection
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
        showCalendarError()
        $('.calendarDropdown').slideUp(0);
        return false
    }   
    $('.LeadsCalendar i').removeClass('icon-downarrow').addClass('icon-close');
    $('.LeadsCalendar').attr('value',getDates);
    $('.LeadsCalendar span').html('Dates');
    $(this).closest('.dropdownOptions').slideUp(50);
    $('#multiCalendar').multiDatesPicker('resetDates');
    $('.calendarDropdown').slideUp(0);
    loadLeads()
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
        loadLeads()
    }
    if(el.closest('a.selected-text').hasClass('Referral'))
    {
        el.prev('span').html('Referral');
        loadLeads()
    }
    else if(el.closest('a.selected-text').hasClass('LeadsCalendar'))
    {
        $('.LeadsCalendar span').html('This month');
        $('.dateSelected').html(window.thisMonth);
        loadLeads()
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
        var agentStatus = $('.agentStatus').attr('value');
        var agentReferral = $('.Referral').attr('value');
        var agentDate = $('.LeadsCalendar').attr('value');
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

                    // Bind Array into Html
                    var setHtml = '';
                    
                    // Check for which user has less leads
                    
                    var getLeadsLength = [];
                    for (var i = 0; i < leads.length; i++) {
                        
                        getLeadsLength.push(leads[i].count);
                    }
                    var getSmallestNumber =  Math.min.apply(null, getLeadsLength);

                    var k = 1;
                    var l = 1;
                    var a = 0;
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
                        
                        setHtml += '<div class="agentImg-container"><img alt="Profile image" class="ProfileImg" src="/profile_image/'+ getUserimage  +'" /></div>';
                        setHtml += '<label class="robotolight fs-18  full one-half-gap-top">'+ leads[i].idOfUser  +'</label>';
                        setHtml += '</div>';
                        
                        // Setting Status Counts
                        var statusOpen = 0;
                        var statusClosed = 0;
                        var statusDealClosed = 0;
                        var statusAll = 0;

                        for (var j = 0; j < leads[i].items.length; j++) {

                            statusAll++
                            if(leads[i].items[j].lead_status == "Closed")
                                {
                                   statusClosed++
                                }
                                else if(leads[i].items[j].lead_status == "Deal closed")
                                {
                                    statusDealClosed++
                                }
                                else
                                {
                                    statusOpen++
                                }

                        }

                        setHtml += '<div class="full fs-14 robotomedium half-pad-top">';
                        setHtml += '<label class="display-inline-block border-count-white">'+statusOpen+'</label>';
                        setHtml += '<label class="display-inline-block border-count-green hide">'+statusDealClosed+'</label>';
                        setHtml += '<label class="display-inline-block border-count-red">'+statusClosed+'</label>';
                        setHtml += '<label class="display-inline-block border-count-blue">'+statusAll+'</label>';
                        setHtml += '</div>';
                        
                        // Setting Agent Leads

                        setHtml += '<div class="full triple-pad-left triple-pad-right triple-pad-top triple-pad-bottom "><ul class="lead-list full lh-38">';
                        
                        // Adding next inline lead
                        if(getSmallestNumber == leads[i].count && l == 1)
                        {
                            //setHtml += '<li class="bg-nextline">Next in line <img alt="Profile image" src="/images/ic_addplus.png"></li>';
                            //l++;
                        }
                        
                            // Loop for leads
                            var status = '';
                            var referral = '';
                            for (var j = 0; j < leads[i].items.length; j++) {

                                // Setting Status

                                if(leads[i].items[j].lead_status == "Closed")
                                {
                                   status = 'bg-red1';
                                }
                                else if(leads[i].items[j].lead_status == "Deal closed")
                                {
                                    status = 'bg-green1';
                                }
                                else
                                {
                                    status = 'bg-white';
                                }
                                // Setting refferal

                                if(leads[i].items[j].referral == "Google")
                                {  referral = '/images/ic-google.png'  }
                                else if(leads[i].items[j].referral == "Word of mouth")
                                {  referral = '/images/ic_wordMouth.png'  }
                                else if(leads[i].items[j].referral == "Previous client")
                                {  referral = '/images/ic_pClient.png'  }
                                else if(leads[i].items[j].referral == "Walk In")
                                {  referral = '/images/ic_walkIn.png'  }
                                else if(leads[i].items[j].referral == "Facebook")
                                {  referral = '/images/ic_facebook.png'  }
                                else
                                {  referral = '/images/ic_other.png'  }

                                // Binding Set Html Leads
                                
                                if(leads[i].items[j].lead_status == "Open")
                                {
                                   var getLeadPopup = $('#closeLead').html();
                                   
                                   setHtml += '<li class="relative userLeadId '+status+'"  userleadId="'+leads[i].items[j].id+'"><p class="absolute closeLeadClick">Close</p><div style="display:none" class="closeLeadPopup absolute full" leadId="'+ leads[i].items[j].id+'"><span class="closeLeadError opacity0 transition-ease-05 color-red">Please fill all fields</span>'+getLeadPopup+'</div><div class="leadUserName ellipsis">'+leads[i].items[j].first_name+ ' ' +leads[i].items[j].last_name + '</div> <img class="referralImage" alt="Profile image" src="'+referral+'" /></li>';
                                }
                                else
                                {
                                   var getLeadPopup = $('#closeLead').html();
                                   
                                   setHtml += '<li class="relative userLeadId '+status+'"  userleadId="'+leads[i].items[j].id+'"><p class="absolute closeLeadClick">Open</p><div style="display:none" class="closeLeadPopup absolute full" leadId="'+ leads[i].items[j].id+'"><span class="closeLeadError opacity0 transition-ease-05 color-red">Please fill all fields</span>'+getLeadPopup+'</div><div class="leadUserName ellipsis">'+leads[i].items[j].first_name+ ' ' +leads[i].items[j].last_name + '</div> <img class="referralImage" alt="Profile image" src="'+referral+'" /></li>';                                  
                                }

                            }

                        setHtml += '</ul></div>';

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

                    }, 1500);


            } // End Success Response
            
        }); // End Ajax Call

    }, 500);

} // End Function

loadLeads()

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


$(document).on('click','#dateRange', function (e) {
    $('.calendarLeave ').removeClass('maxHeightHide');
});

function loadAddNewLeaveCalendar(){

     $('.leaveCalendar').multiDatesPicker('resetDates');
     $(".leaveCalendar").datepicker( "destroy" );
     $(".leaveCalendar").multiDatesPicker({
       onSelect:function(data, event){

        var getValues = [];
        var checkValue = $('#dateRange').val();

        $('.calendarLeave table td.ui-state-highlight').each(function(){
          var getDate = parseInt($(this).find('a').text());
          var getMonth = parseInt($(this).attr('data-month'));
          var getYear = parseInt($(this).attr('data-year'));
          getMonth++
          //var setLeaveDate = getMonth+'/'+getDate+'/'+getYear;
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
          //if(getDate <= 9)
          // {
          //     setLeaveDate = getYear + "-0" + getMonth + "-0" + getDate + ' ';
          // }
          // else
          // {
          //     setLeaveDate = getYear + "-" + getMonth + "-" + getDate + ' ';
          // }
          
          getValues.push(setLeaveDate);
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
            el.closest('.closeLeadPopup').find('.calendarClose ').removeClass('hide');
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
       
        $lead_status =  $('.closeStatus').val();
        $lead_id =  $(".closeLeadPopup").attr("leadid");
        $lead_date = $('.closeLeadDate').attr('value');
        $.ajax({
        type: "POST",
        url: "dashboard/ajaxUpdateleadStatus",
        data: {lead_id : $lead_id , lead_status : $lead_status , lead_date : $lead_date },
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
              
            
                // Append Agent list into agent dropdown
                $('.editDetails').attr('lead-id',parsed[0].id)
                if(parsed[0].lead_status == "Closed")
                {
                    $('.editDetails').addClass('disabled');
                }
                var html = "";
         ////  [{"id":"5","first_name":"test","last_name":"lead","phone_number":"87587765765","email":"test@hotmail.com","product":"Wedding Band","referral":"Word of mouth","special_instructions":"453425","budget":"$2-5k","reference_product":"","contact_method":"Phone call","assign_to":"test farrukh 2-5","reson_skip_next_in_line":"Reason ","lead_status":"Open","lead_owner":"44","create_date":"2018-02-11 01:22:25","booking_date":"2018-02-19"}]
                
                
               html += "<p><label>First Name:</label><label>" + parsed[0].first_name.replace(/'/g, '"') + "</label></p> ";
               html += "<p><label>Last Name:</label><label>" + parsed[0].last_name + " </label></p> ";
               html += "<p><label>Phone Number:</label><label>" + parsed[0].phone_number + " </label></p> ";
               html += "<p><label>Email:</label><label>" + parsed[0].email + " </label></p> ";
               html += "<p><label>Street:</label><label>" + parsed[0].Street + " </label></p> ";
               html += "<p><label>City:</label><label>" + parsed[0].City + " </label></p> ";
               html += "<p><label>Zip:</label><label>" + parsed[0].Zip + " </label></p> ";
               html += "<p><label>Product:</label><label>" + parsed[0].product + " </label></p> ";
               html += "<p><label>Referral:</label><label>" + parsed[0].referral + " </label></p> ";
               html += "<p><label>Special Instruction:</label><label>" + parsed[0].special_instructions + " </label></p> ";
               html += "<p><label>Budget:</label><label>" + parsed[0].budget + " </label></p> ";
               html += "<p><label>Reference Product:</label><label>" + parsed[0].reference_product + " </label></p> ";
               html += "<p><label>Contact Method :</label><label>" + parsed[0].contact_method + " </label></p> ";
               html += "<p><label>Lead Status:</label><label>" + parsed[0].lead_status + " </label></p> ";
               html += "<p><label>Assign To:</label><label>" + parsed[0].assign_to + " </label></p> ";
               html += "<p><label>Booking Date:</label><label>" + parsed[0].booking_date + " </label></p> ";
               html += "<p><label>Specify requirements:</label><label>" + parsed[0].specify_requirements + " </label></p> ";
               
                $('.leadDeailInnerContainer div').html(html);
                $('.leadsContainer').addClass('hide');
                $('.leadDeailContainer').removeClass('hide');

            }

        });    
        
});


// open close lead container
$(document).on('click','.closeLeadClick', function (e) {
       
      var el =$(this);
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

      $('.calendarClose ').addClass('maxHeightHide');

});
    
    
    
        
        
    