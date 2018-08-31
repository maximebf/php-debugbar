# Contributing to PHP DebugBar

First of all, thank you for contributing!

All contributions are welcome as long as they come through pull requests.
Each PR will be reviewed and eventually accepted. Some changes may be required before integration.

A contribution must follow the following guidelines:

 - PHP code convention must follow PSR-1
 - Javascript code convention described below
 - Unit tests must be added/updated and pass
 - Docs must be updated for new features
 - Changelog must be updated (bug fix are not required to be added to the changelog)
 - ensure that examples in demo/ folder are still working

Javascript code convention:

 - based on: http://javascript.crockford.com/code.html
 - blank lines between attributes of a class declaration
 - jsdoc comments
 - all code in a file must be wrapped into an anonymous function
 - variables representing jquery objects must be prefixed with a dollar sign
