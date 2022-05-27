#Key board that recognises who's home

![Whats all this?](img/mountedWithKeys.jpg?raw=true "What this is")  

Under the hood, there is a Shelly Uni 

![Shelly Uni](img/shellyUni.png?raw=true "Shelly Uni")
that has a fixed (Reserved DHCP lease) IP Adress.

It has a 19V DC Powersupply attatched, that goes throgh a Voltage devider: 2,4k Ohms + 1k 2k, 3k and/or 4k for the values of the parallel resisors.
![Schema](img/electicalSchema.png?raw=true "Electrical Schema") the output voltage refers to, how many plugs are put in. Or to be more precise, what combination of plugs is "in" right now. The resolution of the analog digital convetzet is good enough to find out any combination (the shortest distance between two states is like ~0.3V as you can see in www/resistors...csv file.. 

There is also a png file, that has that first sketch, "drawn" into 5 States with several sub - states. These were at the end measured with a regular voltmeter, and put into a csv table, that's then fed into the php file.


Thoughts: http anti api / Mqtt - put logic to node red etc 
