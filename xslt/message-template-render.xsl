<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                    xmlns:xbrli="http://www.xbrl.org/2003/instance"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    xmlns:link="http://www.xbrl.org/2003/linkbase"
                    xmlns:xbrldi="http://xbrl.org/2006/xbrldi"
                    xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
                    xmlns:ebms="http://docs.oasis-open.org/ebxml-msg/ebms/v3.0/ns/core/200704/"
                    xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
                    xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"

            >

        <!-- Match to the root node -->
        <xsl:output method="html"/>

        <xsl:template match="/soap:Envelope">
            <!-- Start the html output and put in the heading stuff -->
            <html>
                <head>
                    <title>ESB Message Envelope</title>
                    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel='stylesheet' type='text/css' />
                    <link href="https://www.compliancetest.net/wp-content/themes/bp-child/css/xslt.css" type="text/css" rel="stylesheet" />
                </head>
                <body>
                    <div id="wrapper">
                        <div id="header-wrapper">
                            <div class="content">
                                <a href="https://www.compliancetest.net" class="logo left"><img src="http://www.compliancetest.net/wp-content/uploads/2013/03/logo.png" /></a>
                            </div>
                        </div>
                        <div id="menu-wrapper"></div>
                        <div id="content-wrapper">
                            <div class="content">
                                <div id="content-inner">
                                    <h2>SOAP Headers Information</h2>

                                    <xsl:call-template name="soapHeaderTable"/>

                                    <h2>XBRL Instance</h2>
                                    <p>The tables below list the data elements and values for each XBRL context found in this instance document. The reference reporting taxonomy is:</p>
                                    <!-- add a reference to the reporting taxonomy - as a hyperlink (hence the xsl:attribute href) -->
                                    <p>
                                        <a>
                                            <xsl:attribute name="href">
                                                <xsl:value-of select="/soap:Body/xbrli:xbrl/link:schemaRef/@xlink:href"/>
                                            </xsl:attribute>
                                            <xsl:value-of select="/soap:Body/xbrli:xbrl/link:schemaRef/@xlink:href"/>
                                        </a>
                                    </p>
                                    <!-- Now start a loop for each xbrl context -->
                                    <xsl:for-each select="/soap:Body/xbrli:xbrl/xbrli:context">
                                        <xsl:sort select="@id"/>
                                        <!-- And call the XSL template to create a table of facts for the context. -->
                                        <xsl:call-template name="factTable">
                                            <!-- And make sure that you collect and pass the context id as a parameter so the table knows which facts to collect -->
                                            <xsl:with-param name="context" select="@id"/>
                                        </xsl:call-template>
                                    </xsl:for-each>
                                    <!-- Close out the html stuff and end the master template -->
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        </xsl:template>

        <xsl:template name="soapHeaderTable">
            <div class="grid-box">
                <div class="grid-box-header"><h3>Security Headers</h3></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr>
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/wsse:Security/wsu:Timestamp/wsu:Created)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/wsse:Security/wsu:Timestamp/wsu:Created/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/wsse:Security/wsu:Timestamp/wsu:Expires)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/wsse:Security/wsu:Timestamp/wsu:Expires/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/wsse:Security/wsse:UsernameToken/wsse:Username)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/wsse:Security/wsse:UsernameToken/wsse:Username/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/wsse:Security/wsse:UsernameToken/wsse:Password)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/wsse:Security/wsse:UsernameToken/wsse:Password/text()"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <h3>ebMS Headers</h3>
            <div class="grid-box">
                <div class="grid-box-header"><h4>Message Info Headers</h4></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:MessageInfo/ebms:Timestamp)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:MessageInfo/ebms:Timestamp/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:MessageInfo/ebms:MessageId)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:MessageInfo/ebms:MessageId"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid-box" style="margin-bottom: 0">
                <div class="grid-box-header"><h4>Party Info Headers: From</h4></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:From/ebms:PartyId)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:From/ebms:PartyId/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:From/ebms:Role)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:From/ebms:Role/text()"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid-box">
                <div class="grid-box-header"><h4>Party Info Headers: To</h4></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:To/ebms:PartyId)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:To/ebms:PartyId/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:To/ebms:Role)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PartyInfo/ebms:To/ebms:Role/text()"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-box">
                <div class="grid-box-header"><h4>Collaboration Info Headers</h4></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:AgreementRef)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:AgreementRef/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:Service)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:Service/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:Action)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:Action/text()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <xsl:value-of select="name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:ConversationId)"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:CollaborationInfo/ebms:ConversationId/text()"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-box">
                <div class="grid-box-header"><h4>Message Properties Headers</h4></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:MessageProperties/ebms:Property">
                                <tr>
                                    <td>
                                        <xsl:value-of select="concat(name(.), ' (@name=', ./@name, ')')"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="./text()"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-box" style="margin-bottom: 0">
                <div class="grid-box-header"><h4>Payload Info Headers</h4></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <xsl:value-of
                                            select="concat(name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PayloadInfo/ebms:PartInfo/ebms:Schema), ' (@', name(/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PayloadInfo/ebms:PartInfo/ebms:Schema/@location),')')"/>
                                </td>
                                <td>
                                    <xsl:value-of select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PayloadInfo/ebms:PartInfo/ebms:Schema/@location"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-box">
                <div class="grid-box-header"><h5>Part Properties Headers</h5></div>
                <div class="grid-box-body">
                    <table cellpadding="0" cellspacing="0" class="format-table">
                        <thead>
                            <tr class="header">
                                <th>Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="/soap:Header/ebms:Messaging/ebms:UserMessage/ebms:PayloadInfo/ebms:PartInfo/ebms:PartProperties/ebms:Property">
                                <tr>
                                    <td>
                                        <xsl:value-of select="concat(name(.), ' (@name=', ./@name, ')')"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="./text()"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </div>
        </xsl:template>


        <!-- This is the fact table template -->
        <xsl:template name="factTable">
            <!-- collect the context id parameter -->
            <xsl:param name="context"/>
            <!-- Start the fact table for this context -->
            <div class="grid-box">
                <div class="grid-box-body">
                    <table cellspacing="0" cellpadding="0" class="format-table">
                        <!-- First add the basic context values here (eg entity & period) -->
                        <thead>
                            <tr class="header">
                                <th>Context Item</th>
                                <th>Context Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Context ID</td>
                                <td>
                                    <xsl:value-of select="@id"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Entity Identifier</td>
                                <td>
                                    <xsl:value-of select="xbrli:entity/xbrli:identifier"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Entity Identifier Scheme</td>
                                <td>
                                    <xsl:value-of select="xbrli:entity/xbrli:identifier/@scheme"/>
                                </td>
                            </tr>
                            <xsl:if test="xbrli:period/xbrli:startDate">
                                <tr>
                                    <td>Period Start Date</td>
                                    <td>
                                        <xsl:value-of select="xbrli:period/xbrli:startDate"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Period End Date</td>
                                    <td>
                                        <xsl:value-of select="xbrli:period/xbrli:endDate"/>
                                    </td>
                                </tr>
                            </xsl:if>
                            <xsl:if test="xbrli:period/xbrli:instant">
                                <tr>
                                    <td>Instant</td>
                                    <td>
                                        <xsl:value-of select="xbrli:period/xbrli:instant"/>
                                    </td>
                                </tr>
                            </xsl:if>
                            <!-- Then add the list of dimension values for this context (removing the namespace prefix) -->
                            <xsl:for-each select="xbrli:entity/xbrli:segment/xbrldi:explicitMember">
                                <xsl:sort select="substring-after(@dimension,':')"/>
                                <tr>
                                    <td>
                                        <xsl:value-of select="substring-after(@dimension,':')"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="substring-after(node(),':')"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                        <!-- Then start the table of facts / values for this context-->
                        <thead>
                            <tr class="header">
                                <th>Fact Name</th>
                                <th>Fact Value</th>
                            </tr>
                        </thead>
                        <!-- Match all elements in the entire document that have a context ref for this context and loop through them -->
                        <tbody>
                            <xsl:for-each select="//*[@contextRef=$context]">
                                <!-- And add a table row for the element name (stripped of namespace prefix for clarity) -->
                                <tr>
                                    <td>
                                        <xsl:value-of select="local-name()"/>
                                    </td>
                                    <!-- And add the element value to the table row -->
                                    <td>
                                        <xsl:value-of select="node()"/>
                                    </td>
                                </tr>
                                <!-- Loop back for the next row in the fact table -->
                            </xsl:for-each>
                        </tbody>
                        <!-- End this fact table, add some space, and go back for the next context table -->
                    </table>
                </div>
            </div>
            <p/>
            <p/>
        </xsl:template>
        <!-- And that's it!  All done :-) Close the stylesheet -->
    </xsl:stylesheet>
