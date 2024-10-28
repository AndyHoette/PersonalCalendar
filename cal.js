// noinspection EqualityComparisonWithCoercionJS

const dayDivs = document.getElementsByClassName("day");
const monthLabel = document.getElementById("monthLabel");
const logInButton = document.getElementById("logInButton");
const createAccountButton = document.getElementById("createAccountButton"); //this is a lot of element we use a lot so we declare are vars
const dayOfEventsH1 = document.getElementById("dayOfEvent");
const eventSelected = document.getElementById("eventSelected");
const listOfEvents = document.getElementById("listOfEvents");
const dot = document.getElementsByClassName("divWithDot")[0];
const logOutButton = document.getElementById("logOut");
const listOfAllDivs = document.getElementsByTagName("div");
const monthColors = ["CadetBlue", "Crimson", "DarkOliveGreen", "DarkTurquoise", "Green", "GoldenRod", "HotPink", "LightSalmon", "OrangeRed", "DimGray", "SaddleBrown", "SeaGreen"]; //this is list of colors for months
let eventDivs = []; //this we set later in the code to be all the eventDivs
let currDate = new Date(); //this is our date variable that defaults to right now
let yearsVisited = [2024]; //this is an arr of years we visited (used for recurring)
let debug = false;

function daysInMonth(month, year){ //this just returns the number of days in a month
    let newDate = new Date(year, month+1, 0);
    return newDate.getDate();
}

function setUp(){
    setUpCalendar();
    if(localStorage.getItem("userID") != null){
        document.getElementById("LogInForm").style.display = "none";
        document.getElementById("createAccountForm").style.display = "none";
        document.getElementById("userGreeting").style.display = "block"; //show the next level
        document.getElementById("AddEventForm").style.display = "block";
        document.getElementById("logOut").style.display = "block";
        document.getElementById("userGreeting").innerHTML = "Hello User #" + localStorage.getItem("userID");
    }
}

function setUpCalendar() { //this is the main display function that sets up the calendar and adds a blue dot for all days with an event
    if(!localStorage.yearsVisited){ //sets the yearsVisited as a local variable
        localStorage.setItem("yearsVisited", JSON.stringify(yearsVisited));
    }
    else{
        yearsVisited = JSON.parse(localStorage.getItem("yearsVisited")); //otherwise update the arr
    }
    changeCalendarColor(currDate.getMonth()); //deals with the color
    monthLabel.innerHTML = mString(currDate) + " " + currDate.getFullYear(); //prints the title of calendar
    let firstDayOfMonth = new Date(currDate.getFullYear(), currDate.getMonth(), 1).getDay();
    let maxDays = daysInMonth(currDate.getMonth(), currDate.getFullYear());
    //console.log(firstDayOfMonth+maxDays);
    for (let i = 0; i < dayDivs.length; i++) { //this loop creates a p tag in all the day cells and makes it say the right number
        dayDivs[i].innerHTML = "";
        dayDivs[i].appendChild(document.createElement("p"));
        if (i < firstDayOfMonth) {
            dayDivs[i].firstElementChild.innerHTML = "";
        } else if (i < firstDayOfMonth + maxDays) {
            dayDivs[i].firstElementChild.innerHTML = i - firstDayOfMonth + 1;
        } else {
            dayDivs[i].firstElementChild.innerHTML = "";
        }
    }
    if(localStorage.getItem("csrfToken")==null){ //if we don't have a csrfToken we aren't logged in -> we have no events to display
        return;
    }
    const data = { 'monthIndex': currDate.getMonth(), 'yearIndex': currDate.getFullYear(), "csrfToken":localStorage.getItem("csrfToken") };
    fetch("getEventsForMonth.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(listOfDates => { //gets a list of dates that have events
            for(let i = 0; i<dayDivs.length; i++){ //makes all days clickable
                dayDivs[i].addEventListener('click', function(){displayDate(this)}, false); //this is a comment
            }
            for(let i = 0; i<listOfDates.length; i++){ //adds the dot if there was an event
                let dayOfEvent = listOfDates[i];
                let clonedDot = dot.cloneNode(true);
                clonedDot.style.display = "block";
                dayDivs[dayOfEvent-1+firstDayOfMonth].appendChild(clonedDot);
            }
        })
        .catch(err => console.error(err));
}

function changeCalendarColor(monthIdx){
    //for all the divs change the color
    for(let i = 0; i<listOfAllDivs.length; i++){
        listOfAllDivs[i].style.color = monthColors[monthIdx];
    }
}

function mString(dateO){ //this is just a helper function because its long
    return dateO.toLocaleString('default',{month: 'long'});
}

function nextMonth(){ //this calculates the new month
    currDate = new Date(currDate.getFullYear(), currDate.getMonth()+1, 1);
    if(currDate.month===0){
        checkNewYear(); //if its a new year check if we've visited it before
    }
    setUpCalendar();
}

function prevMonth() { //this calculates the new month
    currDate = new Date(currDate.getFullYear(), currDate.getMonth()-1, 1);
    if(currDate.month===11){
        checkNewYear(currDate.getFullYear()); //if its a new year check if we've visited it before
    }
    setUpCalendar();
}

function checkNewYear(year){
    yearsVisited = JSON.parse(localStorage.getItem("yearsVisited")); //gets the locally stored var
    if(yearsVisited.includes(year)){ //if we've seen this before
        return; //do nothing
    }
    yearsVisited.push(year); //else add it
    localStorage.setItem("yearsVisited", JSON.stringify(yearsVisited)); //add it the locally stored var
    if(localStorage.getItem("csrfToken")==null){ //if we aren't logged in no need to continue
        return;
    }
    const data = {"newYear": year, "csrfToken":localStorage.getItem("csrfToken") };
    fetch("newYear.php", { //otherwise update all of the recurring events to happen this year
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    }).then((response) => setUpCalendar())
        .catch(err => console.error(err));
}

function displayDate(dateDivInst){
    if(dateDivInst.firstElementChild.innerHTML===""){ //if we clicked an empty square on the calendar
        return; //stop
    }
    document.getElementById("dayListing").style.display = "block"; //show the day listing
    dayOfEventsH1.innerHTML = mString(currDate) + ", " + dateDivInst.firstElementChild.innerHTML + " " + currDate.year; //make it say the day
    listOfEvents.innerHTML = ""; //makes the list blank
    const data= {"yearIndex":currDate.getFullYear(), "monthIndex":currDate.getMonth(), "dayIndex":dateDivInst.firstElementChild.innerHTML};
    if(localStorage.getItem("csrfToken")==null){ //if we don't have a csrfToken (aren't logged in) we can stop
        return;
    }
    fetch("getEventsForDay.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(events => { //returns a list for events for that date
            for(let i = 0; i<events.length; i++){ //for all of these events makes a <li> <div> 7 hidden <input>s and a <p>
                let listItem = document.createElement("li");
                let itemDiv = document.createElement("div");
                itemDiv.className = "eventListed";
                listItem.appendChild(itemDiv);
                let hidden1 = document.createElement("input");
                hidden1.type = "hidden";
                hidden1.value = events[i].id;
                itemDiv.appendChild(hidden1);
                let hidden2 = document.createElement("input");
                hidden2.type = "hidden";
                hidden2.value = events[i].title;
                itemDiv.appendChild(hidden2);
                let hidden3 = document.createElement("input");
                hidden3.type = "hidden";
                hidden3.value = events[i].year;
                itemDiv.appendChild(hidden3);
                let hidden4 = document.createElement("input");
                hidden4.type = "hidden";
                hidden4.value = events[i].month;
                itemDiv.appendChild(hidden4);
                let hidden5 = document.createElement("input");
                hidden5.type = "hidden";
                hidden5.value = events[i].day;
                itemDiv.appendChild(hidden5);
                let hidden6 = document.createElement("input");
                hidden6.type = "hidden";
                hidden6.value = events[i].hour;
                itemDiv.appendChild(hidden6);
                let hidden7 = document.createElement("input");
                hidden7.type = "hidden";
                hidden7.value = events[i].minute;
                itemDiv.appendChild(hidden7);
                let itemP = document.createElement("p");
                itemP.innerHTML = hidden2.value;
                itemDiv.appendChild(itemP);
                listOfEvents.append(listItem);
                //console.log(listItem);
            }
            eventDivs = document.getElementsByClassName('eventListed')
            for(let i = 0; i<eventDivs.length; i++){
                eventDivs[i].addEventListener('click', highlightEvent, false); //makes each event clickable
            }
        })
        .catch(err => console.error(err));
}

function logIn(){
    //checks for valid login
    console.log("attempting login");
    const password = document.getElementById("password").value;
    const userIdLogIn = document.getElementById("user").value;
    const data = {"userID": userIdLogIn, "password":password};
    console.log(data);
    if(debug){
        document.getElementById("LogInForm").style.display = "none";
        document.getElementById("createAccountForm").style.display = "none";
        document.getElementById("userGreeting").style.display = "block"; //show the next level
        document.getElementById("AddEventForm").style.display = "block";
    }
    else {
        console.log(JSON.stringify(data));
        fetch("login_ajax.php", {
            method: 'POST',
            headers: { 'content-type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(answer => { //returns a csrfToken, userID, and success bool
                console.log("this is a print");
                console.log(answer);
                if (answer.success) {
                    console.log("log in good");
                    document.getElementById("LogInForm").style.display = "none";
                    document.getElementById("createAccountForm").style.display = "none";
                    document.getElementById("userGreeting").style.display = "block"; //show the next level
                    document.getElementById("AddEventForm").style.display = "block";
                    localStorage.setItem("csrfToken", answer.csrfToken); //set the local vars correctly
                    localStorage.setItem("userID", answer.user_id);
                    document.getElementById("userGreeting").innerHTML = "Hello User #" + answer.user_id;
                } else {
                    console.log(`You were not logged in: ${data.message}`);
                }
            })
            .catch(err => console.error(err));
    }
    // document.getElementById("LogInForm").style.display = "none";
    // document.getElementById("createAccountForm").style.display = "none";
    // document.getElementById("userGreeting").style.display = "block";
    // document.getElementById("AddEventForm").style.display = "block";
}

function logOut(){ //logs the user out
    fetch("logout.php", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    }).then(response => {
        document.getElementById("AddEventForm").style.display = "none";
        document.getElementById("userGreeting").style.display = "none";
        document.getElementById("eventSelected").style.display = "none";
        document.getElementById("dayListing").style.display = "none";
        document.getElementById("logOut").style.display = "none";
        document.getElementById("createAccountForm").style.display = "block";
        document.getElementById("LogInForm").style.display = "block";
        document.getElementById("user").value = "";
        document.getElementById("password").value = "";
        document.getElementById("createPassword").value = ""
        localStorage.clear(); //clears the local storage
        setUpCalendar();
        })
        .catch(err => console.log(err));
}

function createAccount(b){ //creates a user
    let data = {"password":document.getElementById("createAccountForm").children[1].value}; //gets the password
    console.log(data);
    fetch("createAccount.php", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(response => response.json())
        .then(answer => {
            console.log(answer);
            if(answer.success){
                document.getElementById("LogInForm").style.display = "none";
                document.getElementById("createAccountForm").style.display = "none";
                document.getElementById("userGreeting").style.display = "block"; //show the next level
                document.getElementById("AddEventForm").style.display = "block";
                document.getElementById("logOut").style.display = "block";
                localStorage.setItem("csrfToken", answer.token); //set the local vars correctly
                localStorage.setItem("userID", answer.user_id);
                document.getElementById("userGreeting").innerHTML = "Hello User #" + answer.user_id;
            }
        }).catch(err => console.log(err));
}

function checkValidDate(year, month, day, hour, minute){
    if(year<0 || year>65535){
        return false;
    }
    else if(month<0 || month>11){
        return false;
    }
    else if(day<0 || day>daysInMonth(month, year)){
        return false;
    }
    else if(hour<0 || hour>23){
        return false;
    }
    else if(minute<0 || minute>59){
        return false;
    }
    return true;
}

function addEvent(){
    let newTitle = document.getElementById("title").value;
    let year = document.getElementById("year");
    let month = document.getElementById("month");
    let day = document.getElementById("day");
    let hour = document.getElementById("hour");
    let minute = document.getElementById("minute");
    if(!checkValidDate(year,month,day,hour,minute)){
        return;
    }
    const dateToAdd = {"year":year, "month":month, "day":day, "hour":hour, "minute":minute};
    if(document.getElementById("recurringCheckBox").checked){ //if its supposed to recurring send it there
        addEventRecurring(newTitle, dateToAdd);
    }
    else{
        addEventAJAX(newTitle, dateToAdd, false);
    }
}

function addEventRecurring(title, dateToAdd){
    for(let i = 0; i<yearsVisited.length; i++){
        let newDateToAdd = dateToAdd;
        newDateToAdd.year = yearsVisited[i]
        if(i===0){
            addEventAJAX(title, newDateToAdd, true); //makes only the first instance of it recurring
        }
        else{
            addEventAJAX(title, newDateToAdd, false);
        }
    }
}

function addEventAJAX(title, dateToAdd, recurring){
    if(localStorage.getItem("csrfToken")==null){
        return;
    }
    const data = {"title":title, "year":dateToAdd.year, "month":dateToAdd.month, "day":dateToAdd.day, "hour":dateToAdd.hour, "minute":dateToAdd.minute, "csrfToken":localStorage.getItem("csrfToken"), "recurring": recurring };
    fetch("addEvent.php", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(response => setUpCalendar()) //must set up the calendar after we add an event
        .catch(err => console.log(err));
}

function highlightEvent(){ //this shows the event in the bottom right
    eventSelected.style.display = "block";
    eventSelected.children[0].value = this.children[0].value; //the 7 hidden values
    eventSelected.children[1].value = this.children[1].value; //title
    eventSelected.children[2].value = this.children[2].value; //year
    eventSelected.children[3].value = this.children[3].value; //month
    eventSelected.children[4].value = this.children[4].value; //day
    eventSelected.children[5].value = this.children[5].value; //hour
    eventSelected.children[6].value = this.children[6].value; //minute

    eventSelected.children[9].value = eventSelected.children[1].value; //sets first thing to
    eventSelected.children[12].value = eventSelected.children[2].value; //y
    eventSelected.children[14].value = eventSelected.children[3].value;
    eventSelected.children[16].value = eventSelected.children[4].value; //d
    eventSelected.children[18].value = eventSelected.children[5].value;
    eventSelected.children[20].value = eventSelected.children[6].value;
    document.getElementById("nameOfEvent").innerHTML = this.children[3].value + this.children[4].value + " at " + this.children[5].value +":" + this.children[6].value + ": " + this.children[1].value;
}

function editEvent(){
    if(eventSelected.children[9].value == false || eventSelected.children[12].value == false ||eventSelected.children[14].value == false || eventSelected.children[16].value == false || eventSelected.children[18].value == false || eventSelected.children[20].value == false){ //if any field is blank stop
        return;
    }
    if(localStorage.getItem("csrfToken")==null){
        return;
    }
    const data = {"eventID":this.parentElement.children[0].value, "newTitle":this.parentElement.children[9].value, "year":eventSelected.children[12].value, "month":eventSelected.children[14].value, "day":eventSelected.children[16].value, "hour":eventSelected.children[18].value, "minute":eventSelected.children[20].value,"csrfToken" : localStorage.getItem("csrfToken")};
    fetch("editEvent.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    }).then(result => setUpCalendar()) //need to re - set up the calendar
        .catch(err => console.error(err));
}

function shareEvent(){ //shares the event to a user
    console.log(this.parentElement.children[0].value); //event ID
    console.log(this.parentElement.children[11].value); //new user
    if(localStorage.getItem("csrfToken")==null){
        return;
    }
        const data = {"eventID":this.parentElement.children[0].value, "newOwner":this.parentElement.children[11].value, "csrfToken" : localStorage.getItem("csrfToken")};
    fetch("shareEvent.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    }).then(result => setUpCalendar()) //need to re - set up the calendar
        .catch(err => console.error(err));
}

function deleteEvent(){ //deletes the event
    console.log(this.parentElement.children[0].value); //event ID
    if(localStorage.getItem("csrfToken")==null) {
        return;
    }
    const data = {"eventID":this.parentElement.children[0].value, "csrfToken" : localStorage.getItem("csrfToken")};
    fetch("deleteEvent.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    }).then(result => setUpCalendar()) //need to re - set up the calendar
        .catch(err => console.error(err));
}

document.getElementById("AddEventForm").style.display = "none";
document.getElementById("userGreeting").style.display = "none";
document.getElementById("eventSelected").style.display = "none"; //sets the user logged in screen to hidden on start
document.getElementById("dayListing").style.display = "none";
document.getElementById("logOut").style.display = "none";
dot.style.display = "none";

document.addEventListener("DOMContentLoaded", setUp, false);

document.getElementById("prevMonthButton").addEventListener('click', prevMonth, false);
document.getElementById("nextMonthButton").addEventListener('click', nextMonth, false);
document.getElementById("eventEditButton").addEventListener('click', editEvent, false);
document.getElementById("eventShareButton").addEventListener('click', shareEvent, false);
document.getElementById("eventDeleteButton").addEventListener('click', deleteEvent, false);
logInButton.addEventListener("click", (e)=>{e.preventDefault(); logIn();}, false);
document.getElementById('addEventButton').addEventListener("click", (e) =>{e.preventDefault(); addEvent();}, false);
createAccountButton.addEventListener("click", (e) =>{e.preventDefault(); createAccount(createAccountButton);}, false);
logOutButton.addEventListener("click", logOut, false);