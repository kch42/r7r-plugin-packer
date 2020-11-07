#!/usr/bin/env php
<?php

/*
 * File: r7r-plugin-packer.php
 * Packaging a Ratatöskr plugin.
 */

require_once(dirname(__FILE__) . "/pluginpackage.php");

/* Parse options */
$options = getopt("", array(
    "codefile:",
    "classname:",
    "pluginname:",
    "author:",
    "versiontext:",
    "versioncount:",
    "updatepath:",
    "web:",
    "api:",
    "licensefile:",
    "helpfile:",
    "shortdesc:",
    "custompub:",
    "custompriv:",
    "tpldir:",
    "output:"
));

$usage = <<<USAGE
Usage:
${argv[0]} options...

Mandatory options:
  --output=FILE     Where should the output be saved?
  --codefile=FILE   The PHP file with the plugin code.
  --classname=CLASS The name of the RatatoeskrPlugin implementation.
  --pluginname=NAME The name of the plugin (it is recommended to use some kind of developer prefix to make the name more unique).
  --author=AUTHOR   Your name (preferably in the form: My Name<my-mail-address@example.com>).
  --versiontext=VER A short text, that describes this version (something like: 1.0 beta).
  --versioncount=C  A number that increases with every release.
  --api=APIVER      The version number of the plugin API.
  --shortdesc=DESC  A short description of your plugin. You can use #hashtags.

Optional options:
  --updatepath=URL  A URL where Ratatöskr can check, if there is a new version (URL should point to a serialize()'d array("current-version" => VERSIONCOUNT, "dl-path" => DOWNLOAD PATH); Will get overwritten by the default repository software).
  --web=HOMEPAGE    Homepage of the Plugin.
  --licensefile=FILE    Should a license be included?
  --helpfile=FILE   A HTML file that acts as a help/manual for your plugin.
  --custompub=DIR   Directory that contains custom public(i.e. can later be accessed from the web) data.
  --custompriv=DIR  Directory that contains custom private data.
  --tpldir=DIR      Directory that contains templates used by this plugin.

USAGE
;

if(!(isset($options["output"]) and isset($options["codefile"]) and isset($options["classname"]) and isset($options["pluginname"]) and isset($options["author"]) and isset($options["versiontext"]) and isset($options["versioncount"]) and isset($options["api"]) and isset($options["shortdesc"])))
{
    fprintf(STDERR, "Missing options\n\n" . $usage);
    exit(1);
}

$code = file_get_contents($options["codefile"]);
if($code === FALSE)
{
    fprintf(STDERR, "Can not open '${options['codefile']}'.\n");
    exit(1);
}

/* Remove trailing <?php ?> delimiters */
$code = preg_replace('/^<\?php/s', "", $code);
$code = preg_replace('/\?>\s*$/s', "", $code);

$plugin = new PluginPackage();

$plugin->code              = $code;
$plugin->classname         = $options["classname"];
$plugin->name              = $options["pluginname"];
$plugin->author            = $options["author"];
$plugin->versiontext       = $options["versiontext"];
$plugin->versioncount      = $options["versioncount"];
$plugin->api               = $options["api"];
$plugin->short_description = $options["shortdesc"];

if(isset($options["updatepath"]))
    $plugin->updatepath = $options["updatepath"];
if(isset($options["web"]))
    $plugin->web = $options["web"];
if(isset($options["licensefile"]))
    $plugin->license = @file_get_contents($options["licensefile"]);
if(isset($options["helpfile"]))
    $plugin->help = @file_get_contents($options["helpfile"]);
if(isset($options["custompub"]))
    $plugin->custompub = dir2array($options["custompub"]);
if(isset($options["custompriv"]))
    $plugin->custompriv = dir2array($options["custompriv"]);
if(isset($options["tpldir"]))
    $plugin->tpls = dir2array($options["tpldir"]);

file_put_contents($options["output"], $plugin->save());
