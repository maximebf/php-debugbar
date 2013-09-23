# Changelog

2013-09-23:

 - send the request id in headers and use the open handler to retreive the dataset
 - !! modified sendDataAsHeaders() to add $useOpenHandler as the first argument
 - OpenHandler::handle() can now take any objects implementing ArrayAccess as request data

2013-09-19:

 - added HttpDriver
 - added jQuery.noConflict() managment

2013-09-15 (1.6):

 - added sending data through HTTP headers
 - added stacked data
 - bug fixes

1.5:

 - added storage
 - added open handler
