#Some Old Bullshit#
This is a collection of various scripts that need to be (or are in the process 
of being) ported to the new platform. Generally that means taking bunches of
functions and creating proper objects, leveraging underlying functionality, and
cleaning up scratch code that should never have made it into production as-is.  
  
There's some code that's decent, but most of the core.php stuff was written in
an extreme hurry responding to one last-minute request or another.  
  
Index:
  
##v1corefunctions##
This is a single file of functions that's used by a lot of the early CASH stuff. 
Some newer projects use it too, but mostly for the db connection and little else. 
It's a mess, but a lot of the raw code needed for objects in the DIY platform
has either come from here or will, and creating decent OO code from the functions 
has actually been pretty okay.  
  
##securestreams##
The 'publicfacing' directory is the latest revision of the php/javascript used
to power the secure stream stuff. It's pretty tidy, easy to work with, and aside
from minor dependencies on the core.php file for DB connections I'd say it's 
not awful code. The JS init works nicely with the 1.0 version of the CASH JS
lib (ex-Flower) and we should be able to push this into the platform very nicely.  
  
The 'v1admin' is an utter piece of shit. It won't be hard to improve upon for 
the platform, but I wanted to include reference to the way it was. Screenshots
are included so no one has to go through the pain and suffering of reading the
code.  
  
##misc##
Two small scripts: one to add a defined number of download codes for a specific
asset, and one to upload an asset to s3 with the correct content disposition 
header (attachment) for forcing download instead of in-browser display of media
content on direct request.