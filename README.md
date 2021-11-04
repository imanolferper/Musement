READ ME:

By Imanol Fernández Periañez 

This is a REST API which provide about the weather in each city.
You dont need to install nothing, just upload this to a server and make tests.
It has an easy architecture and is implemented based on a more complex rest api. I have simplified it for simple reasons of use, in this case nothing more is necessary. In this way, it will be more understandable for the developer who has to create it.
In case of proposing improvements, it is easy to find the place where they can be made. You can also add new endpoints, and global functions, apart from a cache.

We have 2 methods, the first one is valid to insert in the Musement api the values of the weather in the next days.
The second just provide you the weather of a selected city in the selected date.

ENDPOINTS

Insert the weather:

POST /app/cities/

Params: 

	-city (String) required
	-info (Array of Strings) required

The city is the code of the city we want to insert, we must check if the city exists.
Info in array of strings, each string is the next day weather starting for today, if we just insert 1, it will insert the weather for today, but if we insert more than one, this will insert in the next days.

Posible code returns:

	- 200 OK
	- 480 Invalid data
	- 492 error inserting this date

--------------------------------------------------

GET /app/cities/

Params: 

	-city (String) required
	-date (String) required

The city is the code of the city we want to check.
Date is string in a format (Y-m-d) of the date that we want to know, if the format is different, this will return an error of invalid data. We can add to the call of checkDate function the format in that we want to check the date.

Posible code returns:

	- 200 OK
	- 480 Invalid data

--------------------------------------------------


The api will return 480 invalidad data if the info provided is limited or have invalid format.

