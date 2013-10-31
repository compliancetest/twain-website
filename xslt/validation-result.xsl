<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"                    
                    xmlns:soapenv="http://www.w3.org/2003/05/soap-envelope"
                    xmlns:event.02.data="http://sbr.gov.au/comn/event.02.data" 
                    xmlns:TSFPI.00.00="http://sbr.gov.au/dims/TSFPI.00.00.dims" 
                    xmlns:SSFPI.00.00="http://sbr.gov.au/dims/SSFPI.00.00.dims" 
                    xmlns:mr.0001.initiaterollover.req.02.00="http://sbr.gov.au/rprt/super/mr/mr.0001.initiaterollover.request.02.00.report" 
                    xmlns:xl="http://www.xbrl.org/2003/XLink" 
                    xmlns:link="http://www.xbrl.org/2003/linkbase" 
                    xmlns:saxon="http://saxon.sf.net/" xmlns:xs="http://www.w3.org/2001/XMLSchema" 
                    xmlns:SourceSuperFundABN.02.00="http://sbr.gov.au/dims/SourceSuperFundABN.02.00.dims" 
                    xmlns:address3.02.01="http://sbr.gov.au/comnmdle/comnmdle.addressdetails3.02.01.module" 
                    xmlns:wsa="http://www.w3.org/2005/08/addressing" 
                    xmlns:regexpFunctions="java:net.compliancetest.xslt.extention.RegexpFuntions" 
                    xmlns:dateFunctions="java:net.compliancetest.xslt.extention.DateFunctions" 
                    xmlns:emsup.02.08="http://sbr.gov.au/icls/em/emsup/emsup.02.08.data" 
                    xmlns:SSFAMI.00.00="http://sbr.gov.au/dims/SSFAMI.00.00.dims" 
                    xmlns:xbrli="http://www.xbrl.org/2003/instance" 
                    xmlns:emsup.02.03="http://sbr.gov.au/icls/em/emsup/emsup.02.03.data" 
                    xmlns:email1.02.00="http://sbr.gov.au/comnmdle/comnmdle.electroniccontactelectronicmail1.02.00.module" 
                    xmlns:definitionFunctions="java:net.compliancetest.xslt.extention.DefinitionFunctions" 
                    xmlns:pyde.02.08="http://sbr.gov.au/icls/py/pyde/pyde.02.08.data" 
                    xmlns:iso="http://purl.oclc.org/dsdl/schematron" 
                    xmlns:RcvSprFndAbn.02.00="http://sbr.gov.au/dims/RcvSprFndAbn.02.00.dims" 
                    xmlns:TargetSuperFundABN.02.00="http://sbr.gov.au/dims/TargetSuperFundABN.02.00.dims" 
                    xmlns:TsfrSprFndAbn.02.00="http://sbr.gov.au/dims/TsfrSprFndAbn.02.00.dims" 
                    xmlns:prsnstrcnm1.02.00="http://sbr.gov.au/comnmdle/comnmdle.personstructuredname1.02.00.module" 
                    xmlns:orgname1.02.00="http://sbr.gov.au/comnmdle/comnmdle.organisationname1.02.00.module" 
                    xmlns:tech="http://sbr.gov.au/fdtn/sbr.01.02.tech" 
                    xmlns:i="http://www.w3.org/2001/XMLSchema-instance" 
                    xmlns:xbrldi="http://xbrl.org/2006/xbrldi" 
                    xmlns:phone1.02.00="http://sbr.gov.au/comnmdle/comnmdle.electroniccontacttelephone1.02.00.module" 
                    xmlns:s="http://www.w3.org/2003/05/soap-envelope" 
                    xmlns:pyde.02.00="http://sbr.gov.au/icls/py/pyde/pyde.02.00.data" 
                    xmlns:xlink="http://www.w3.org/1999/xlink" 
                    xmlns:pyde.02.01="http://sbr.gov.au/icls/py/pyde/pyde.02.01.data" 
                    xmlns:numericFunctions="java:net.compliancetest.xslt.extention.NumericFunctions" 
                    xmlns:RprtPyType.02.03="http://sbr.gov.au/dims/RprtPyType.02.03.dims" 
                    xmlns:core="http://sbr.gov.au/comn/core.02.data" 
                    xmlns:pyid.02.00="http://sbr.gov.au/icls/py/pyid/pyid.02.00.data" 
                    xmlns:sch="http://www.ascc.net/xml/schematron" 
                    xmlns:pyid.02.05="http://sbr.gov.au/icls/py/pyid/pyid.02.05.data" 
                    xmlns:iso4217="http://www.xbrl.org/2003/iso4217"
                    xmlns:arelle="http://arelle.org/xbrl/validation/xml"
            >

<!-- Match to the root node -->
<xsl:output method="html"/>

<xsl:template match="/">
    <!-- Start the html output and put in the heading stuff -->
    <html>
        <head>
            <title>ESB Validation Result</title>
            <link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700' rel='stylesheet' type='text/css' />
            <link href="/wp-content/themes/bp-child/css/xslt.css" type="text/css" rel="stylesheet" />            
        </head>
        <body>
            <div id="wrapper">             
                <div id="header-wrapper">
                    <div class="content">
                        <a href="https://www.compliancetest.net" class="logo left"><img src="/wp-content/uploads/2013/03/logo.png" /></a>
                    </div>
                </div>
                <div id="menu-wrapper"></div>
                <div id="content-wrapper">
                    <div class="content">
                        <div id="content-inner">                            
                            <xsl:apply-templates />
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
</xsl:template>

<!-- ************************************************************* Default Validation Results  ************************************************************* -->
<xsl:template match="/event.02.data:EventItems">
    <h2>ESB Validation Result</h2>
    <xsl:apply-templates />
</xsl:template>

<xsl:template name="eventItemTemplate" match="/event.02.data:EventItems/event.02.data:EventItem">
    <div class="grid-box">
        <div class="grid-box-header"><h3><xsl:value-of select="name()"/></h3></div>
        <div class="grid-box-body">
            <table cellpadding="0" cellspacing="0" class="format-table">
                <thead>
                    <tr>
                        <th>Identifier</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><xsl:value-of select="name(event.02.data:Error.Code)" /></td>
                        <td><xsl:value-of select="event.02.data:Error.Code/text()" /></td>
                    </tr>
                    <tr>
                        <td><xsl:value-of select="name(event.02.data:Severity.Code)" /></td>
                        <td><xsl:value-of select="event.02.data:Severity.Code/text()" /></td>
                    </tr>
                    <tr>
                        <td><xsl:value-of select="name(event.02.data:Short.Description)" /></td>
                        <td><xsl:value-of select="event.02.data:Short.Description/text()" /></td>
                    </tr>
                    <tr>
                        <td><xsl:value-of select="name(event.02.data:Detailed.Description)" /></td>
                        <td><xsl:value-of select="event.02.data:Detailed.Description/text()" /></td>
                    </tr>
                    <tr>
                        <td><xsl:value-of select="name(event.02.data:Parameters)" /></td>
                        <td>
                            <table cellpadding="0" cellspacing="0" class="format-table1">
                                <tr>
                                    <th>Identifier</th>
                                    <th>Value</th>
                                </tr>
                                <xsl:for-each select="event.02.data:Parameters/event.02.data:Parameter">
                                    <tr>
                                        <td><div class="break-all"><xsl:value-of select="event.02.data:Parameter.Identifier/text()" /></div></td>
                                        <td><xsl:value-of select="event.02.data:Parameter.Text/text()" /></td>
                                    </tr>
                                </xsl:for-each>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><xsl:value-of select="name(event.02.data:Locations/event.02.data:Location.Instance.Identifier)" /></td>
                        <td><xsl:value-of select="event.02.data:Locations/event.02.data:Location.Path.Text/text()" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</xsl:template>

<!-- ************************************************************* Arelle ************************************************************* -->
<xsl:template name="evenTemplate" match="/arelle:log/arelle:entry">
    <div class="grid-box arelle-entry-box">
        <div class="grid-box-header">
            <h4>
                <span class="entry-code">Entry Code: <b><xsl:value-of select="@code" /></b></span>
                <span class="entry-level">Level: <b><xsl:value-of select="@level" /></b></span>
                <div class="clear"></div>
            </h4>            
        </div>
        <div class="grid-box-body">            
            <xsl:apply-templates />
           
        </div>
    </div>
</xsl:template>
<xsl:template match="arelle:entry/arelle:message">
    <table cellpadding="0" cellspacing="0" class="format-table">
        <thead>
            <tr class="header">
                <th>
                    Message
                    <xsl:if test="@contextID">
                    (<xsl:value-of select="name(@contextID)"/>: <xsl:value-of select="@contextID"/>)
                    </xsl:if>
                </th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <xsl:value-of select="text()"/>
            </td>
        </tr>                
        </tbody>
    </table>
</xsl:template>


<xsl:template match="arelle:entry/arelle:ref">
    <table cellpadding="0" cellspacing="0" class="format-table">
        <thead>
            <tr class="header">
                <th>
                    Ref
                    (<xsl:if test="@href">
                        <xsl:value-of select="name(@href)"/>: <b><xsl:value-of select="@href"/></b>
                    </xsl:if>                            
                    <xsl:if test="@sourceLine">
                        <span style="float: right"><xsl:value-of select="name(@sourceLine)"/>: <b><xsl:value-of select="@sourceLine"/> </b></span>
                    </xsl:if>)
                </th>
            </tr>
        </thead>
        <tbody>
        <xsl:if test="boolean(arelle:property)">
            <tr>
                <td>                
                    <b>Properties:</b>                    
                    <table class="format-table1" cellpadding="0" cellspacing="0" width="936" style="table-layout: fixed;">
                        <tr>
                            <th>Name</th>
                            <th width="90%">Value</th>
                        </tr>

                            <xsl:apply-templates /> 
                    </table>
                </td>
            </tr>
        </xsl:if>                   
        </tbody>
    </table>    
</xsl:template>

<xsl:template match="arelle:ref/arelle:property">
    <tr>
        <td><xsl:value-of select="@name" /></td>
        <td width="70%">
            <div style="overflow: auto">
                <xsl:value-of select="@value" />
                <xsl:if test="boolean(.)">
                    <table class="format-table1" cellpadding="0" cellspacing="0" width="100%">
                        <xsl:apply-templates />
                    </table>
                </xsl:if>
            </div>
        </td>
    </tr>    
</xsl:template>

<xsl:template match="arelle:property">
    <tr>
        <td>
            <div style="overflow: auto; word-break:break-all;">
                <xsl:value-of select="@name" />
            </div>            
        </td>
        <td width="70%">
            <div style="overflow: auto; word-break:break-all; ">
                <xsl:value-of select="@value" />                            
                <xsl:if test="boolean(.)">
                    <table class="format-table1" cellpadding="0" cellspacing="0" width="100%">
                        <xsl:apply-templates />
                    </table>
                </xsl:if>
            </div>
        </td>
    </tr>                   
</xsl:template>

</xsl:stylesheet>