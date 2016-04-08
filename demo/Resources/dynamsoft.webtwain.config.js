//
// Dynamsoft JavaScript Library for Basic Initiation of Dynamic Web TWAIN
// More info on DWT: http://www.dynamsoft.com/Products/WebTWAIN_Overview.aspx
//
// Copyright 2015, Dynamsoft Corporation 
// Author: Dynamsoft Team
// Version: 11.2
//
/// <reference path="dynamsoft.webtwain.initiate.js" />
var Dynamsoft = Dynamsoft || { WebTwainEnv: {} };

Dynamsoft.WebTwainEnv.AutoLoad = true;
///
Dynamsoft.WebTwainEnv.Containers = [{ContainerId:'dwtcontrolContainer', Width:270, Height:350}];
///
Dynamsoft.WebTwainEnv.ProductKey = '24DF83F90F8AA33B3F82F6BA34A95A44BB9D0F089BBB593D8D728E123141C1ADBB9D0F089BBB593D55B08CBBC41C11C4BB9D0F089BBB593D130170990A950949BB9D0F089BBB593D61242D1D88AF648FBB9D0F089BBB593D765655BB96104432BB9D0F089BBB593D5F0C64C79881C418BB9D0F089BBB593DC566D7FC44AD33D7BB9D0F089BBB593D1467E3DA4A4782DEBB9D0F089BBB593D802118300B6509FCBB9D0F089BBB593DEC2E22797E548017BB9D0F089BBB593D8B6B70EF84D7C147BB9D0F089BBB593DEA73043718282165BB9D0F089BBB593D9DEB5E6FF195791EBB9D0F089BBB593DB57694842702F234E0000000';
///
Dynamsoft.WebTwainEnv.Trial = true;
///
Dynamsoft.WebTwainEnv.ActiveXInstallWithCAB = false;
///
Dynamsoft.WebTwainEnv.Debug = false; // only for debugger output
///
// Dynamsoft.WebTwainEnv.ResourcesPath = 'Resources';

/// All callbacks are defined in the dynamsoft.webtwain.install.js file, you can customize them.

// Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainReady', function(){
// 		// webtwain has been inited
// });

