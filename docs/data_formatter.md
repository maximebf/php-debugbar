# Data Formatter

An instance of `DebugBar\DataFormatter\DataFormatterInterface` is used by collectors to
format data.

The default instance is `DebugBar\DataFormatter\DataFormatter`. This can be modified
using `DebugBar\DataCollector\DataCollector::setDefaultDataFormatter()`.

You can use a custom formater for each collector using `DataCollector::setDataFormatter()`.