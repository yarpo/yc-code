codecitationREADME.txt

The CodeCitation plugin allows you to include code sections in your Joomla 1.5 content using {codecitation} or custom tag.
Use of the {codecitation} tag allows you to work with code sections in your Joomla WYSIWIG editors.

Plugin uses SyntaxHighlighter 2.0 library from http://alexgorbatchev.com/wiki/SyntaxHighlighter

Supports: syntax highlighting for ActionScript3, Bash/shell, C#, C++, CSS, Delphi, Diff, Groovy, JavaScript, Java, JavaFX, Perl, PHP, Plain Text, PowerShell, Python, Ruby, Scala, SQL, Visual Basic, XML, HTML, XSLT

INSTALLATION INSTRUCTIONS:
====================

- download the codecitation plugin.
- install the plugin using the Joomla Extensions install utility.
- ENABLE the new CodeCitation plugin with the Joomla 1.5 backend plugin manager.
- Set default parameters for the plugin engine.

USING THE PLUGIN:
The syntax for the usage is:

{codecitation [class="<parameters for SyntaxHighligh engine>"] [width="<>"]}
<text to be formatted goes here>
{/codecitation}

where:
  <parameters for SyntaxHighligh engine> is a parameter string for the engine - see http://www.smartsoftwarebits.com/index.php/codecitation/69-supported-brushes-and-parameters
  <width> is an optional parameter to specify <div> width (fix code in some cases into your template)
For more information about SintaxHighlighter engine parameters please visit http://www.smartsoftwarebits.com/index.php/codecitation

EXAMPLES
{codecitation class="brush: xml; gutter: false;" width="500px"}
xlm goes here
{/codecitation}

OR (if you set default language to C++, for example):

{codecitation}
C++ code goes here
{/codecitation}

OR if you set custom tag=code in plugin parameters and default language=C++:
{code}
C++ code goes here
{/code}

VERSION 1.0
========
  - initial release

VERSION 1.1.0
========
  - PHP 5.2.3 callback syntax workaround

VERSION 1.2.0
========
  - Core engine updated to version 2.0.320 of SyntaxHighlighter

VERSION 1.3.1
========
  - Page load speed improved and page size redused. Only necessary scripts are loaded.
  - Custom tag support implemented. Plugin supports custom plugin invocation tag in case "codecitation" seems to long for somebody (i use "code" tag time to time).
  - Default language parameter implemented and can be set on the plugin's parameters page. It allows simplified plugin invocation systax.
  - Default engine parameters values can be set on the plugin's parameters page.
  - Themes are changeble on plugin paameters page.
