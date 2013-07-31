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
            >

<!-- Match to the root node -->
<xsl:output method="html"/>

<xsl:template match="/">
    <!-- Start the html output and put in the heading stuff -->
    <html>
        <head>
            <title>ESB Validation Result</title>
            <style type="text/css">
                tr.header {background-color: #AAAAAA; }
            </style>
        </head>
        <body>
            <h2>ESB Validation Result</h2>
            <xsl:for-each select="/soapenv:Body/event.02.data:EventItems/event.02.data:EventItem">            
                <h3><xsl:value-of select="name(/soapenv:Body/event.02.data:EventItems/event.02.data:EventItem)"/></h3>
                <xsl:call-template name="eventItemTemplate"/>
            </xsl:for-each>
        </body>
    </html>
</xsl:template>

<xsl:template name="eventItemTemplate">
    <table border="1" cellpadding="10">
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
                <table border="1">
                    <tr class="header">
                        <th>Identifier</th>
                        <th>Value</th>
                    </tr>
                    <xsl:for-each select="event.02.data:Parameters/event.02.data:Parameter">
                        <tr>
                            <td><xsl:value-of select="event.02.data:Parameter.Identifier/text()" /></td>
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
        
    </table>
</xsl:template>

<!-- And that's it!  All done :-) Close the stylesheet -->
</xsl:stylesheet>