[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/LRsBrD_9)
# CSE330

Andy Hoette - 518194

Josh Lee - 518325

If a date has a dot then it has an event. Click on the day to view all the events in that date. Click on the event text to edit, share, or delete it.

<br>
    Link to Website: http://ec2-18-224-2-189.us-east-2.compute.amazonaws.com/~ahhoette/module5-group-module5-518194-518325/
<br>
We have 3 things for our creative portion: <br>
    -We have annual recurring events<br>
    -We have sharing events<br>
    -Color of calendar changes based on month<br>

<br><br><br><br><br><br>
Needed php Scripts:
    getEventsForMonth.php takes in monthIndex, yearIndex and returns a list unique days (just ints is fine) which contain events<br>
    getEventsForDay.php takes in a monthIndex, dayIndex, and yearIndex and returns a list of all events (title and datetime, and id)<br>
    editEvent.php takes the eventID, newTitle, newDatetime doesn't need to return anything<br>
    deleteEvent.php takes the eventID and doesn't need to return anything<br>
    newYear.php takes newYear and needs to create a new event for all recurring events in that year and doesn't need to return anything<br>
    logout.php which destroys the session<br>
    addEvent.php takes title, newDatetime and doesn't need to return anything<br>
    shareEvent.php takes eventID, newOwner and duplicates the event for the newOwner and doesn't need to return anything<br>
    createUser.php takes a password and creates a new user and needs to return the newUser ID and csrf token
    
    
    
