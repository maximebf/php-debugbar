# Data Formater

An instance of `DebugBar\DataFormater\DataFormaterInterface` is used by collectors to
format data.

The default instance is `DebugBar\DataFormater\DataFormater`. This can be modified
using `DebugBar\DataCollector\DataCollector::setDefaultDataFormater()`.

You can use a custom formater for each collector using `DataCollector::setDataFormater()`.