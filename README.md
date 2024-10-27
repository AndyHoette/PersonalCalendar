[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/LRsBrD_9)
# CSE330

Andy Hoette - 518194

Josh Lee - 518325

What needs to be done:
    I need to adjust Login ajax request to fit our code <br>
    We need to be able to share event given an eventID and userID to share to<br>
    We need to be able to delete events given an eventID<br>
    We need to be able to edit events given an eventID, the new Title, new Datetime<br>
    We need to be able to create an account given a password and send back the ID<br>
    We need to be able to get all the events from a certain month in a format similar to {id: 330, title:"This is obj title", when:new Date()}<br>
    We need to be able to add an Event in every year in the yearsVisited database<br>
    We need to be able to add every recurring event for a given years<br>
    I need to make it so that events that repeat yearly all get added when going to a new year<br>
    I need to make the color change when change the month (looks cool)<br>

Needed php Scripts:
    getEventsForMonth.php takes in monthIndex, yearIndex and returns a list unique days (just ints is fine) which contain events<br>
    getEventsForDay.php takes in a monthIndex, dayIndex, and yearIndex and returns a list of all events (titles and datetime)<br>
    editEvent.php takes the eventID, newTitle, newDatetime doesn't need to return anything<br>
    deleteEvent.php takes the eventID and doesn't need to return anything<br>
    newYear.php takes newYear and needs to create a new event for all recurring events in that year and doesn't need to return anything<br>
    logout.php which destroys the session<br>
    addEvent.php takes title, newDatetime and doesn't need to return anything<br>
    shareEvent.php takes eventID, newOwner and duplicates the event for the newOwner and doesn't need to return anything<br>
    
    
    
