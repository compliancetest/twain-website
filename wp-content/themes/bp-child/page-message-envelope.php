<?php
  /***
  * Template Name: Message Envelope
  */

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'xml';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$id){
    echo '<p>Invalid Request!</p>';
    exit;
}

if(!is_user_logged_in())
{
    addMessage('Please login to see the xml', 'error');
    wp_redirect('/');
    exit;
}

$esb = new ManageESB();

$message = $esb->getMessageEnvelope($id);

if(!$message){
    echo '<p>Permission Denied!</p>';
    exit;
}
$message = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:rol="http://compliancetest.net/superannuation/rollover" xmlns:ebms="http://docs.oasis-open.org/ebxml-msg/ebms/v3.0/ns/core/200704/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xsi:schemaLocation="http://docs.oasis-open.org/ebxml-msg/ebms/v3.0/ns/core/200704/ http://docs.oasis-open.org/ebxml-msg/ebms/v3.0/core/ebms-header-3_0-200704.xsd"><soap:Header>
        <wsse:Security>
            <wsu:Timestamp wsu:Id="authTimestamp">
                <!--  Harness Generated  -->
                <wsu:Created>2013-10-14T22:16:56</wsu:Created>
                <!--  Harness Generated  -->
                <wsu:Expires>2013-11-14T22:16:56</wsu:Expires>
            </wsu:Timestamp>
            <wsse:UsernameToken>
                <!--  Message initiator provided  -->
                <wsse:Username/>
                <!--  Message initiator provided  -->
                <wsse:Password/>
            </wsse:UsernameToken>
        </wsse:Security>
        <ebms:Messaging>
            <ebms:UserMessage>
                <ebms:MessageInfo>
                    <!--  Harness Generated  -->
                    <ebms:Timestamp>2013-10-14T22:16:56</ebms:Timestamp>
                    <!--  Harness Generated  -->
                    <ebms:MessageId>urn:uuid:2f75eadd-996b-4b81-ad7c-8cec40d28aee@compliancetest.net</ebms:MessageId>
                </ebms:MessageInfo>
                <ebms:PartyInfo>
                    <ebms:From>
                        <ebms:PartyId type="http://sbr.gov.au/identifier/usi">AMP0195AU</ebms:PartyId>
                        <ebms:Role>
                            http://docs.oasis-open.org/ebxml-msg/ebms/v3.0/ns/core/200704/defaultRole
                        </ebms:Role>
                    </ebms:From>
                    <ebms:To>
                        <ebms:PartyId type="http://sbr.gov.au/identifier/usi">AMI0100AU</ebms:PartyId>
                        <ebms:Role>
                            http://docs.oasis-open.org/ebxml-msg/ebms/v3.0/ns/core/200704/defaultRole
                        </ebms:Role>
                    </ebms:To>
                </ebms:PartyInfo>
                <ebms:CollaborationInfo>
                    <ebms:AgreementRef>http://sbr.gov.au/agreement/Light/1.0/Pull</ebms:AgreementRef>
                    <ebms:Service>http://sbr.gov.au/service/Rollover/1.0</ebms:Service>
                    <ebms:Action>InitiateRolloverRequest</ebms:Action>
                    <!--  Harness Generated  -->
                    <ebms:ConversationId>Rollover.25002981919.61</ebms:ConversationId>
                </ebms:CollaborationInfo>
                <ebms:MessageProperties>
                    <!--  Message initiator provided  -->
                    <ebms:Property name="ProductId">TestExtProductID</ebms:Property>
                </ebms:MessageProperties>
                <ebms:PayloadInfo>
                    <ebms:PartInfo>
                        <ebms:Schema location="http://sbr.gov.au/taxonomy/sbr_au_reports/sprstrm/sprrol/sprrol_0001/sprrol.0001.inititaterollover.request.02.00.report.xsd"/>
                        <ebms:PartProperties>
                            <!--  Harness calculated based on FromPartyId:ToPartyId -->
                            <ebms:Property name="PartID">AMP0195AU:AMI0100AU</ebms:Property>
                            <!--  Default value="76514770399"  -->
                            <ebms:Property name="TargetABN">76514770399</ebms:Property>
                            <!--  Default value="28342064803"  -->
                            <ebms:Property name="SourceABN">28342064803</ebms:Property>
                            <!--  Default value="AMI0100AU"  -->
                            <ebms:Property name="TargetUniqueSuperannuationIdentifier">AMI0100AU</ebms:Property>
                            <!--  Default value="AMP0195AU"  -->
                            <ebms:Property name="SourceUniqueSuperannuationIdentifier">AMP0195AU</ebms:Property>
                        </ebms:PartProperties>
                    </ebms:PartInfo>
                </ebms:PayloadInfo>
            </ebms:UserMessage>
        </ebms:Messaging>
    </soap:Header><soap:Body>
        <xbrli:xbrl xmlns:xbrli="http://www.xbrl.org/2003/instance" xmlns:link="http://www.xbrl.org/2003/linkbase" xmlns:sprrol.0001.prv.02.00="http://sbr.gov.au/rprt/sprstrm/sprrol/sprrol.0001.private.02.00.module" xmlns:address3.02.01="http://sbr.gov.au/comnmdle/comnmdle.addressdetails3.02.01.module" xmlns:dtyp.02.03="http://sbr.gov.au/fdtn/sbr.02.03.dtyp" xmlns:emsup.02.08="http://sbr.gov.au/icls/em/emsup/emsup.02.08.data" xmlns:dtyp.02.09="http://sbr.gov.au/fdtn/sbr.02.09.dtyp" xmlns:TsfrSprFndUSI.02.00_typedelement="http://sbr.gov.au/dims/TsfrSprFndUSI.02.00.dims" xmlns:sprrol.0001.inititaterollover.req.02.00="http://sbr.gov.au/rprt/sprstrm/sprrol/sprrol.0001.inititaterollover.request.02.00.report" xmlns:dtyp.02.00="http://sbr.gov.au/fdtn/sbr.02.00.dtyp" xmlns:email1.02.00="http://sbr.gov.au/comnmdle/comnmdle.electroniccontactelectronicmail1.02.00.module" xmlns:TsfrSprFndAbn.02.00_typedelement="http://sbr.gov.au/dims/TsfrSprFndAbn.02.00.dims" xmlns:emsup.02.03="http://sbr.gov.au/icls/em/emsup/emsup.02.03.data" xmlns:pyde.02.08="http://sbr.gov.au/icls/py/pyde/pyde.02.08.data" xmlns:RcvSprFndUSI.02.00_typedelement="http://sbr.gov.au/dims/RcvSprFndUSI.02.00.dims" xmlns:prsnstrcnm1.02.00="http://sbr.gov.au/comnmdle/comnmdle.personstructuredname1.02.00.module" xmlns:orgname1.02.00="http://sbr.gov.au/comnmdle/comnmdle.organisationname1.02.00.module" xmlns:xbrldt="http://xbrl.org/2005/xbrldt" xmlns:dtyp.02.13="http://sbr.gov.au/fdtn/sbr.02.13.dtyp" xmlns:xbrldi="http://xbrl.org/2006/xbrldi" xmlns:tech.01.03="http://sbr.gov.au/fdtn/sbr.01.03.tech" xmlns:ref="http://www.xbrl.org/2006/ref" xmlns:TsfrSprFndAllMemId.02.00_typedelement="http://sbr.gov.au/dims/TsfrSprFndAllMemId.02.00.dims" xmlns:phone1.02.00="http://sbr.gov.au/comnmdle/comnmdle.electroniccontacttelephone1.02.00.module" xmlns:RcvSprFndAbn.02.00_typedelement="http://sbr.gov.au/dims/RcvSprFndAbn.02.00.dims" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:pyde.02.00="http://sbr.gov.au/icls/py/pyde/pyde.02.00.data" xmlns:RprtPyType.02.06="http://sbr.gov.au/dims/RprtPyType.02.06.dims" xmlns:pyde.02.01="http://sbr.gov.au/icls/py/pyde/pyde.02.01.data" xmlns:tech.01.02="http://sbr.gov.au/fdtn/sbr.01.02.tech" xmlns:dtyp.02.15="http://sbr.gov.au/fdtn/sbr.02.15.dtyp" xmlns:pyid.02.05="http://sbr.gov.au/icls/py/pyid/pyid.02.05.data" xmlns:iso4217="http://www.xbrl.org/2003/iso4217" xsi:schemaLocation="http://xbrl.org/2006/xbrldi http://www.xbrl.org/2006/xbrldi-2006.xsd">
            <!--
             Default @xlink:href="http://sbr.gov.au/taxonomy/sbr_au_reports/sprstrm/sprrol/sprrol_0001/sprrol.0001.inititaterollover.request.02.00.report.xsd" 
            -->
            <link:schemaRef xlink:href="http://sbr.gov.au/taxonomy/sbr_au_reports/sprstrm/sprrol/sprrol_0001/sprrol.0001.inititaterollover.request.02.00.report.xsd" xlink:type="simple"/>
            <!--  Sender context  -->
            <xbrli:context id="SND01">
                <xbrli:entity>
                    <xbrli:identifier scheme="http://www.abr.gov.au/abn">31008414104</xbrli:identifier>
                    <xbrli:segment>
                        <xbrldi:explicitMember dimension="RprtPyType.02.06:ReportPartyTypeDimension">RprtPyType.02.06:MessageSender</xbrldi:explicitMember>
                    </xbrli:segment>
                </xbrli:entity>
                <xbrli:period>
                    <!--  Harness generated  -->
                    <xbrli:startDate>2013-10-13T22:16:56</xbrli:startDate>
                    <!--  Harness generated  -->
                    <xbrli:endDate>2013-10-14T22:16:56</xbrli:endDate>
                </xbrli:period>
            </xbrli:context>
            <!--  Receiver context  -->
            <xbrli:context id="RCR01">
                <xbrli:entity>
                    <xbrli:identifier scheme="http://www.abr.gov.au/abn">25002981919</xbrli:identifier>
                    <xbrli:segment>
                        <xbrldi:explicitMember dimension="RprtPyType.02.06:ReportPartyTypeDimension">RprtPyType.02.06:MessageReceiver</xbrldi:explicitMember>
                    </xbrli:segment>
                </xbrli:entity>
                <xbrli:period>
                    <!--  Harness generated  -->
                    <xbrli:startDate>2013-10-13T22:16:56</xbrli:startDate>
                    <!--  Harness generated  -->
                    <xbrli:endDate>2013-10-14T22:16:56</xbrli:endDate>
                </xbrli:period>
            </xbrli:context>
            <xbrli:context id="MBRROLLVR01">
                <xbrli:entity>
                    <xbrli:identifier scheme="http://www.ato.gov.au/tfn">123456774</xbrli:identifier>
                    <xbrli:segment>
                        <xbrldi:explicitMember dimension="RprtPyType.02.06:ReportPartyTypeDimension">RprtPyType.02.06:SuperFundMember</xbrldi:explicitMember>
                        <xbrldi:typedMember dimension="TsfrSprFndAllMemId.02.00_typedelement:TransferringSuperFundAllocatedMemberIDDimension">
                            <TsfrSprFndAllMemId.02.00_typedelement:SuperannuationFundDetails.MemberClient.Identifier>KER214</TsfrSprFndAllMemId.02.00_typedelement:SuperannuationFundDetails.MemberClient.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="TsfrSprFndAbn.02.00_typedelement:TransferringSuperFundABNDimension">
                            <TsfrSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>76514770399</TsfrSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="RcvSprFndAbn.02.00_typedelement:ReceivingSuperFundABNDimension">
                            <RcvSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>28342064803</RcvSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="RcvSprFndUSI.02.00_typedelement:ReceivingSuperannuationFundUniqueSuperannuationIdentifierDimension">
                            <RcvSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>AMP0195AU</RcvSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="TsfrSprFndUSI.02.00_typedelement:TransferringSuperannuationFundUniqueSuperannuationIdentifierDimension">
                            <TsfrSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>AMI0100AU</TsfrSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>
                        </xbrldi:typedMember>
                    </xbrli:segment>
                </xbrli:entity>
                <xbrli:period>
                    <!--  Harness generated  -->
                    <xbrli:startDate>2013-10-13T22:16:56</xbrli:startDate>
                    <!--  Harness generated  -->
                    <xbrli:endDate>2013-10-14T22:16:56</xbrli:endDate>
                </xbrli:period>
            </xbrli:context>
            <xbrli:context id="MBRROLLVR02">
                <xbrli:entity>
                    <xbrli:identifier scheme="http://www.sbr.gov.au/id">SAPO412</xbrli:identifier>
                    <xbrli:segment>
                        <xbrldi:explicitMember dimension="RprtPyType.02.06:ReportPartyTypeDimension">RprtPyType.02.06:SuperFundMember</xbrldi:explicitMember>
                        <xbrldi:typedMember dimension="TsfrSprFndAllMemId.02.00_typedelement:TransferringSuperFundAllocatedMemberIDDimension">
                            <TsfrSprFndAllMemId.02.00_typedelement:SuperannuationFundDetails.MemberClient.Identifier>SAPO412</TsfrSprFndAllMemId.02.00_typedelement:SuperannuationFundDetails.MemberClient.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="TsfrSprFndAbn.02.00_typedelement:TransferringSuperFundABNDimension">
                            <TsfrSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>76514770399</TsfrSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="RcvSprFndAbn.02.00_typedelement:ReceivingSuperFundABNDimension">
                            <RcvSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>28342064803</RcvSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="RcvSprFndUSI.02.00_typedelement:ReceivingSuperannuationFundUniqueSuperannuationIdentifierDimension">
                            <RcvSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>AMP0195AU</RcvSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="TsfrSprFndUSI.02.00_typedelement:TransferringSuperannuationFundUniqueSuperannuationIdentifierDimension">
                            <TsfrSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>AMI0100AU</TsfrSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>
                        </xbrldi:typedMember>
                    </xbrli:segment>
                </xbrli:entity>
                <xbrli:period>
                    <!--  Harness generated  -->
                    <xbrli:startDate>2013-10-13T22:16:56</xbrli:startDate>
                    <!--  Harness generated  -->
                    <xbrli:endDate>2013-10-14T22:16:56</xbrli:endDate>
                </xbrli:period>
            </xbrli:context>
            <xbrli:context id="MBRROLLVR03">
                <xbrli:entity>
                    <xbrli:identifier scheme="http://www.sbr.gov.au/id">TEJO456</xbrli:identifier>
                    <xbrli:segment>
                        <xbrldi:explicitMember dimension="RprtPyType.02.06:ReportPartyTypeDimension">RprtPyType.02.06:SuperFundMember</xbrldi:explicitMember>
                        <xbrldi:typedMember dimension="TsfrSprFndAllMemId.02.00_typedelement:TransferringSuperFundAllocatedMemberIDDimension">
                            <TsfrSprFndAllMemId.02.00_typedelement:SuperannuationFundDetails.MemberClient.Identifier>TEJO456</TsfrSprFndAllMemId.02.00_typedelement:SuperannuationFundDetails.MemberClient.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="TsfrSprFndAbn.02.00_typedelement:TransferringSuperFundABNDimension">
                            <TsfrSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>76514770399</TsfrSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="RcvSprFndAbn.02.00_typedelement:ReceivingSuperFundABNDimension">
                            <RcvSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>28342064803</RcvSprFndAbn.02.00_typedelement:Identifiers.AustralianBusinessNumber.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="RcvSprFndUSI.02.00_typedelement:ReceivingSuperannuationFundUniqueSuperannuationIdentifierDimension">
                            <RcvSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>AMP0195AU</RcvSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>
                        </xbrldi:typedMember>
                        <xbrldi:typedMember dimension="TsfrSprFndUSI.02.00_typedelement:TransferringSuperannuationFundUniqueSuperannuationIdentifierDimension">
                            <TsfrSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>AMI0100AU</TsfrSprFndUSI.02.00_typedelement:SuperannuationFundDetails.UniqueSuperannuationIdentifier.Identifier>
                        </xbrldi:typedMember>
                    </xbrli:segment>
                </xbrli:entity>
                <xbrli:period>
                    <!--  Harness generated  -->
                    <xbrli:startDate>2013-10-13T22:16:56</xbrli:startDate>
                    <!--  Harness generated  -->
                    <xbrli:endDate>2013-10-14T22:16:56</xbrli:endDate>
                </xbrli:period>
            </xbrli:context>
            <xbrli:unit id="AUS">
                <xbrli:measure>iso4217:AUD</xbrli:measure>
            </xbrli:unit>
            <!-- too many occurrence of OrganisationNameDetails tuple for Sender context-->
            <orgname1.02.00:OrganisationNameDetails>
                <pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code contextRef="SND01">MN</pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code>
                <pyde.02.00:OrganisationNameDetails.OrganisationalName.Text contextRef="SND01">CT Harness Member Fund</pyde.02.00:OrganisationNameDetails.OrganisationalName.Text>
            </orgname1.02.00:OrganisationNameDetails>
            <orgname1.02.00:OrganisationNameDetails>
                <pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code contextRef="SND01">MN</pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code>
                <pyde.02.00:OrganisationNameDetails.OrganisationalName.Text contextRef="SND01">CT Harness Member Fund</pyde.02.00:OrganisationNameDetails.OrganisationalName.Text>
            </orgname1.02.00:OrganisationNameDetails>
            <!-- too many occurrence of PersonNameDetails tuple for Sender context-->
            <prsnstrcnm1.02.00:PersonNameDetails>
                <pyde.02.00:PersonNameDetails.FamilyName.Text contextRef="SND01">Jones</pyde.02.00:PersonNameDetails.FamilyName.Text>
                <pyde.02.00:PersonNameDetails.GivenName.Text contextRef="SND01">Sally</pyde.02.00:PersonNameDetails.GivenName.Text>
            </prsnstrcnm1.02.00:PersonNameDetails>
            <prsnstrcnm1.02.00:PersonNameDetails>
                <pyde.02.00:PersonNameDetails.FamilyName.Text contextRef="SND01">Jones</pyde.02.00:PersonNameDetails.FamilyName.Text>
                <pyde.02.00:PersonNameDetails.GivenName.Text contextRef="SND01">Sally</pyde.02.00:PersonNameDetails.GivenName.Text>
            </prsnstrcnm1.02.00:PersonNameDetails>
            <!-- too many occurrence of ElectronicContactElectronicMail tuple for Sender context-->
            <email1.02.00:ElectronicContactElectronicMail>
                <pyde.02.00:ElectronicContact.ElectronicMail.Usage.Code contextRef="SND01">03</pyde.02.00:ElectronicContact.ElectronicMail.Usage.Code>
                <pyde.02.00:ElectronicContact.ElectronicMail.Address.Text contextRef="SND01">sally.jones@hilltop.com.au</pyde.02.00:ElectronicContact.ElectronicMail.Address.Text>
            </email1.02.00:ElectronicContactElectronicMail>
            <email1.02.00:ElectronicContactElectronicMail>
                <pyde.02.00:ElectronicContact.ElectronicMail.Usage.Code contextRef="SND01">03</pyde.02.00:ElectronicContact.ElectronicMail.Usage.Code>
                <pyde.02.00:ElectronicContact.ElectronicMail.Address.Text contextRef="SND01">sally.jones@hilltop.com.au</pyde.02.00:ElectronicContact.ElectronicMail.Address.Text>
            </email1.02.00:ElectronicContactElectronicMail>
            <!-- too many occurrence of ElectronicContactTelephone tuple for Sender context-->
            <phone1.02.00:ElectronicContactTelephone>
                <pyde.02.00:ElectronicContact.Telephone.Usage.Code contextRef="SND01">03</pyde.02.00:ElectronicContact.Telephone.Usage.Code>
                <pyde.02.00:ElectronicContact.Telephone.ServiceLine.Code contextRef="SND01">02</pyde.02.00:ElectronicContact.Telephone.ServiceLine.Code>
                <pyde.02.00:ElectronicContact.Telephone.Area.Code contextRef="SND01">03</pyde.02.00:ElectronicContact.Telephone.Area.Code>
                <pyde.02.00:ElectronicContact.Telephone.Minimal.Number contextRef="SND01">41258745</pyde.02.00:ElectronicContact.Telephone.Minimal.Number>
            </phone1.02.00:ElectronicContactTelephone>
            <phone1.02.00:ElectronicContactTelephone>
                <pyde.02.00:ElectronicContact.Telephone.Usage.Code contextRef="SND01">03</pyde.02.00:ElectronicContact.Telephone.Usage.Code>
                <pyde.02.00:ElectronicContact.Telephone.ServiceLine.Code contextRef="SND01">02</pyde.02.00:ElectronicContact.Telephone.ServiceLine.Code>
                <pyde.02.00:ElectronicContact.Telephone.Area.Code contextRef="SND01">03</pyde.02.00:ElectronicContact.Telephone.Area.Code>
                <pyde.02.00:ElectronicContact.Telephone.Minimal.Number contextRef="SND01">41258745</pyde.02.00:ElectronicContact.Telephone.Minimal.Number>
            </phone1.02.00:ElectronicContactTelephone>
            <!-- too many occurrence of OrganisationalNameType tuple for Receiver context-->
            <orgname1.02.00:OrganisationNameDetails>
                <pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code contextRef="RCR01">MN</pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code>
                <pyde.02.00:OrganisationNameDetails.OrganisationalName.Text contextRef="RCR01">Hilltop Member Fund</pyde.02.00:OrganisationNameDetails.OrganisationalName.Text>
            </orgname1.02.00:OrganisationNameDetails>
            <orgname1.02.00:OrganisationNameDetails>
                <pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code contextRef="RCR01">MN</pyde.02.00:OrganisationNameDetails.OrganisationalNameType.Code>
                <pyde.02.00:OrganisationNameDetails.OrganisationalName.Text contextRef="RCR01">Hilltop Member Fund</pyde.02.00:OrganisationNameDetails.OrganisationalName.Text>
            </orgname1.02.00:OrganisationNameDetails>
            <prsnstrcnm1.02.00:PersonNameDetails>
                <pyde.02.00:PersonNameDetails.FamilyName.Text contextRef="MBRROLLVR01">John</pyde.02.00:PersonNameDetails.FamilyName.Text>
                <pyde.02.00:PersonNameDetails.GivenName.Text contextRef="MBRROLLVR01">Kerrigan</pyde.02.00:PersonNameDetails.GivenName.Text>
                <pyde.02.00:PersonNameDetails.OtherGivenName.Text contextRef="MBRROLLVR01">Adam</pyde.02.00:PersonNameDetails.OtherGivenName.Text>
            </prsnstrcnm1.02.00:PersonNameDetails>
            <pyde.02.00:PersonDemographicDetails.Sex.Code contextRef="MBRROLLVR01">1</pyde.02.00:PersonDemographicDetails.Sex.Code>
            <pyde.02.00:PersonDemographicDetails.Birth.Date contextRef="MBRROLLVR01">1958-05-12</pyde.02.00:PersonDemographicDetails.Birth.Date>
            <!-- too many occurrence of AddressDetails tuple for MRT context-->
            <address3.02.01:AddressDetails>
                <pyde.02.01:AddressDetails.Usage.Code contextRef="MBRROLLVR01">POS</pyde.02.01:AddressDetails.Usage.Code>
                <pyde.02.00:AddressDetails.Line1.Text contextRef="MBRROLLVR01">Grand Apartment</pyde.02.00:AddressDetails.Line1.Text>
                <pyde.02.00:AddressDetails.Line2.Text contextRef="MBRROLLVR01">Block D</pyde.02.00:AddressDetails.Line2.Text>
                <pyde.02.00:AddressDetails.Line3.Text contextRef="MBRROLLVR01">Unit 1</pyde.02.00:AddressDetails.Line3.Text>
                <pyde.02.00:AddressDetails.Line4.Text contextRef="MBRROLLVR01">25 St Georges Terrace</pyde.02.00:AddressDetails.Line4.Text>
                <pyde.02.00:AddressDetails.LocalityName.Text contextRef="MBRROLLVR01">Perth</pyde.02.00:AddressDetails.LocalityName.Text>
                <pyde.02.00:AddressDetails.Postcode.Text contextRef="MBRROLLVR01">6000</pyde.02.00:AddressDetails.Postcode.Text>
                <pyde.02.00:AddressDetails.StateOrTerritory.Code contextRef="MBRROLLVR01">WA</pyde.02.00:AddressDetails.StateOrTerritory.Code>
                <pyde.02.08:AddressDetails.Country.Code contextRef="MBRROLLVR01">au</pyde.02.08:AddressDetails.Country.Code>
            </address3.02.01:AddressDetails>
            <address3.02.01:AddressDetails>
                <pyde.02.01:AddressDetails.Usage.Code contextRef="MBRROLLVR01">POS</pyde.02.01:AddressDetails.Usage.Code>
                <pyde.02.00:AddressDetails.Line1.Text contextRef="MBRROLLVR01">Grand Apartment</pyde.02.00:AddressDetails.Line1.Text>
                <pyde.02.00:AddressDetails.Line2.Text contextRef="MBRROLLVR01">Block D</pyde.02.00:AddressDetails.Line2.Text>
                <pyde.02.00:AddressDetails.Line3.Text contextRef="MBRROLLVR01">Unit 1</pyde.02.00:AddressDetails.Line3.Text>
                <pyde.02.00:AddressDetails.Line4.Text contextRef="MBRROLLVR01">25 St Georges Terrace</pyde.02.00:AddressDetails.Line4.Text>
                <pyde.02.00:AddressDetails.LocalityName.Text contextRef="MBRROLLVR01">Perth</pyde.02.00:AddressDetails.LocalityName.Text>
                <pyde.02.00:AddressDetails.Postcode.Text contextRef="MBRROLLVR01">6000</pyde.02.00:AddressDetails.Postcode.Text>
                <pyde.02.00:AddressDetails.StateOrTerritory.Code contextRef="MBRROLLVR01">WA</pyde.02.00:AddressDetails.StateOrTerritory.Code>
                <pyde.02.08:AddressDetails.Country.Code contextRef="MBRROLLVR01">au</pyde.02.08:AddressDetails.Country.Code>
            </address3.02.01:AddressDetails>
            <pyid.02.05:Identifiers.TaxFileNumberNotProvided.Indicator contextRef="MBRROLLVR01">false</pyid.02.05:Identifiers.TaxFileNumberNotProvided.Indicator>
            <emsup.02.08:SuperannuationRollover.TransferWholeBalance.Indicator contextRef="MBRROLLVR01">true</emsup.02.08:SuperannuationRollover.TransferWholeBalance.Indicator>
            <!-- too many occurrence of PersonNameDetails tuple for MRT context-->
            <prsnstrcnm1.02.00:PersonNameDetails>
                <pyde.02.00:PersonNameDetails.FamilyName.Text contextRef="MBRROLLVR02">Adrian</pyde.02.00:PersonNameDetails.FamilyName.Text>
                <pyde.02.00:PersonNameDetails.GivenName.Text contextRef="MBRROLLVR02">Sapolete</pyde.02.00:PersonNameDetails.GivenName.Text>
                <pyde.02.00:PersonNameDetails.OtherGivenName.Text contextRef="MBRROLLVR02"/>
            </prsnstrcnm1.02.00:PersonNameDetails>
            <prsnstrcnm1.02.00:PersonNameDetails>
                <pyde.02.00:PersonNameDetails.FamilyName.Text contextRef="MBRROLLVR02">Adrian</pyde.02.00:PersonNameDetails.FamilyName.Text>
                <pyde.02.00:PersonNameDetails.GivenName.Text contextRef="MBRROLLVR02">Sapolete</pyde.02.00:PersonNameDetails.GivenName.Text>
                <pyde.02.00:PersonNameDetails.OtherGivenName.Text contextRef="MBRROLLVR02"/>
            </prsnstrcnm1.02.00:PersonNameDetails>
            <pyde.02.00:PersonDemographicDetails.Sex.Code contextRef="MBRROLLVR02">1</pyde.02.00:PersonDemographicDetails.Sex.Code>
            <pyde.02.00:PersonDemographicDetails.Birth.Date contextRef="MBRROLLVR02">1999-04-10</pyde.02.00:PersonDemographicDetails.Birth.Date>
            <address3.02.01:AddressDetails>
                <pyde.02.01:AddressDetails.Usage.Code contextRef="MBRROLLVR02">POS</pyde.02.01:AddressDetails.Usage.Code>
                <pyde.02.00:AddressDetails.Line1.Text contextRef="MBRROLLVR02">Unit 3</pyde.02.00:AddressDetails.Line1.Text>
                <pyde.02.00:AddressDetails.Line2.Text contextRef="MBRROLLVR02">15 Franklin Street</pyde.02.00:AddressDetails.Line2.Text>
                <pyde.02.00:AddressDetails.Line3.Text contextRef="MBRROLLVR02"/>
                <pyde.02.00:AddressDetails.Line4.Text contextRef="MBRROLLVR02"/>
                <pyde.02.00:AddressDetails.LocalityName.Text contextRef="MBRROLLVR02">Tottenham</pyde.02.00:AddressDetails.LocalityName.Text>
                <pyde.02.00:AddressDetails.Postcode.Text contextRef="MBRROLLVR02">3012</pyde.02.00:AddressDetails.Postcode.Text>
                <pyde.02.00:AddressDetails.StateOrTerritory.Code contextRef="MBRROLLVR02">VIC</pyde.02.00:AddressDetails.StateOrTerritory.Code>
                <pyde.02.08:AddressDetails.Country.Code contextRef="MBRROLLVR02">au</pyde.02.08:AddressDetails.Country.Code>
            </address3.02.01:AddressDetails>
            <pyid.02.05:Identifiers.TaxFileNumberNotProvided.Indicator contextRef="MBRROLLVR02">true</pyid.02.05:Identifiers.TaxFileNumberNotProvided.Indicator>
            <emsup.02.08:SuperannuationRollover.TransferWholeBalance.Indicator contextRef="MBRROLLVR02">false</emsup.02.08:SuperannuationRollover.TransferWholeBalance.Indicator>
            <emsup.02.08:SuperannuationRollover.Requested.Amount contextRef="MBRROLLVR02" decimals="0" unitRef="AUS">20000</emsup.02.08:SuperannuationRollover.Requested.Amount>
            <prsnstrcnm1.02.00:PersonNameDetails>
                <pyde.02.00:PersonNameDetails.FamilyName.Text contextRef="MBRROLLVR03">NOT APPLICABLE</pyde.02.00:PersonNameDetails.FamilyName.Text>
                <pyde.02.00:PersonNameDetails.GivenName.Text contextRef="MBRROLLVR03">Sutejo</pyde.02.00:PersonNameDetails.GivenName.Text>
                <pyde.02.00:PersonNameDetails.OtherGivenName.Text contextRef="MBRROLLVR03"/>
            </prsnstrcnm1.02.00:PersonNameDetails>
            <pyde.02.00:PersonDemographicDetails.Sex.Code contextRef="MBRROLLVR03">1</pyde.02.00:PersonDemographicDetails.Sex.Code>
            <pyde.02.00:PersonDemographicDetails.Birth.Date contextRef="MBRROLLVR03">1965-12-21</pyde.02.00:PersonDemographicDetails.Birth.Date>
            <address3.02.01:AddressDetails>
                <pyde.02.01:AddressDetails.Usage.Code contextRef="MBRROLLVR03">POS</pyde.02.01:AddressDetails.Usage.Code>
                <pyde.02.00:AddressDetails.Line1.Text contextRef="MBRROLLVR03">Sultera Jambon 3</pyde.02.00:AddressDetails.Line1.Text>
                <pyde.02.00:AddressDetails.Line2.Text contextRef="MBRROLLVR03">No 9</pyde.02.00:AddressDetails.Line2.Text>
                <pyde.02.00:AddressDetails.Line3.Text contextRef="MBRROLLVR03">Alam Sutera</pyde.02.00:AddressDetails.Line3.Text>
                <pyde.02.00:AddressDetails.Line4.Text contextRef="MBRROLLVR03">15310</pyde.02.00:AddressDetails.Line4.Text>
                <pyde.02.00:AddressDetails.LocalityName.Text contextRef="MBRROLLVR03">Tangerang</pyde.02.00:AddressDetails.LocalityName.Text>
                <pyde.02.08:AddressDetails.Country.Code contextRef="MBRROLLVR03">id</pyde.02.08:AddressDetails.Country.Code>
            </address3.02.01:AddressDetails>
            <emsup.02.08:SuperannuationRollover.TransferWholeBalance.Indicator contextRef="MBRROLLVR03">true</emsup.02.08:SuperannuationRollover.TransferWholeBalance.Indicator>
            <pyid.02.05:Identifiers.TaxFileNumberNotProvided.Indicator contextRef="MBRROLLVR03">true</pyid.02.05:Identifiers.TaxFileNumberNotProvided.Indicator>
        </xbrli:xbrl>
    </soap:Body></soap:Envelope>';
header("Content-type: application/xml");
if($mode != 'html'){    
    echo $message;
}else{
    $xslt = get_site_url() . '/xslt/message-envelope.xsl';
    $message = str_replace('?>', '?><?xml-stylesheet type="text/xsl" href="' . $xslt . '"?>', $message);
    echo $message;
}

