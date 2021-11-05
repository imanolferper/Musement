READ ME:

By Imanol Fernández Periañez 

1- The file musement.sh contains the first task of the work.

2- The app folders contains the rest api implemented in PHP.

This is a REST API which provide about the weather in each city.
You dont need to install nothing, just upload this to a server and make tests.
It has an easy architecture and is implemented based on a more complex rest api. I have simplified it for simple reasons of use, in this case nothing more is necessary. In this way, it will be more understandable for the developer who has to create it. Nos is implement in PHP, i have not used any framework.
In case of proposing improvements, it is easy to find the place where they can be made. You can also add new endpoints, and global functions, apart from a cache.

We have 2 methods, the first one is valid to insert in the Musement api the values of the weather in the next days.
The second just provide you the weather of a selected city in the selected date.

ENDPOINTS

Insert the weather:

POST /app/cities/

Params: 

	-city (String) required
	-info (Array of Strings) required

The city is the code of the city we want to insert, we must check if the city exists, the city can be send with capital letters, we will check it.
Info in array of strings, each string is the next day weather starting for today, if we just insert 1, it will insert the weather for today, but if we insert more than one, this will insert in the next days.
If the weather exist's, we will replace the weather with the new one.

Posible code returns:

	- 200 OK
	- 480 Invalid data
	- 491 City no found
	- 492 Error inserting this weather
	

The api will return 480 invalidad data if the info provided is limited or have invalid format.

TESTING:

1 - city: amsterdam, info[]: Cloudly	200
2 - city: amsterda, info[]: Rainy	491
3 - city: amsterdam, info[]:		200
4 - city: amsterdam, info: Rainy	480
5 - city: amsterdam, info[]: Rainy, info[]: Cloudly	200
6 - cit: amsterdam, info[]: Rainy, info[]: Cloudly	480

--------------------------------------------------

GET /app/cities/

Params: 

	-city (String) required
	-date (String) required

The city is the code of the city we want to check, the city must exist, the city can be send with capital letters, we will check it.
Date is string in a format (Y-m-d) of the date that we want to know, if the format is different, this will return an error of invalid data. If is neccesary we can add to the call of checkDate function the format in that we want to check the date. We must check if the date is valid too, if is not valid we will return a 480 error.

Posible code returns:

	- 200 OK
	- 480 Invalid data
	- 491 City no found
	- 493 No data for that date
	

The api will return 480 invalidad data if the info provided is limited or have invalid format.

TESTING:

1 - city: amsterdam, date: 2021-4-8	200
2 - city: amsterdam, date: 2021-04-8	200
3 - city: amsterdam, date: 2021-04-48	480
4 - city: amsterda, date: 2021-04-8	491
5 - cit: amsterdam, date: 2021-04-8	480
6 - city: amsterdam, dat: 2021-04-8	480
7 - city: amsterdam, date: 2041-04-8	493 (It could be 200 code if it exists)


--------------------------------------------------

* It is possible that the rest api created is missing a use case implemented in the test, I would only have to add the checks that have occurred to me while doing the documentation
