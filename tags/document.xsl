<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet [
<!ENTITY nl "&#xd;&#xa;">
]>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xar="http://xaraya.com/2004/blocklayout"
    xmlns:php="http://php.net/xsl"
    exclude-result-prefixes="php xar">

<!--
  <xsl:template match="xar:document">
    <xsl:element name="Document">&#160;
      <xsl:attribute name="xmlns">
        http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd
      </xsl:attribute>
      <xsl:attribute name="xmlns:xsi">
        http://www.w3.org/2001/XMLSchema-instance
      </xsl:attribute>
      <xsl:attribute name="xsi:schemaLocation">
        http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd  pain.001.001.03.ch.02.xsd
      </xsl:attribute>
    </xsl:element>
  </xsl:template>
-->    
  <xsl:template match="xar:document">
    <xsl:element name="CstmrCdtTrfInitn">
        <xsl:call-template name="epayment-header"/>
        <xsl:call-template name="epayment-paymentinfo"/>
    </xsl:element>
  </xsl:template>
  
  <xsl:template name="epayment-header">
    <xsl:element name="GrpHdr">
      <xsl:element name="MsgId">
        placeholder
      </xsl:element>
      <xsl:element name="CreDtTm">
        <xsl:processing-instruction name="php">
          <xsl:text>echo date('Y-m-d\TH:i:sP')</xsl:text>
        </xsl:processing-instruction>
      </xsl:element>
      <xsl:element name="NbOfTxs">
        placeholder
      </xsl:element>
      <xsl:element name="CtrlSum">
        placeholder
      </xsl:element>
      <xsl:element name="InitgPty">
          <xsl:element name="Nm">
            placeholder
          </xsl:element>
      </xsl:element>
    </xsl:element>
  </xsl:template>

  <xsl:template name="epayment-paymentinfo">
    <xsl:element name="PmtInf">
      <xsl:element name="PmtInfId">
        placeholder
      </xsl:element>
      <xsl:element name="PmtMtd">
        placeholder
      </xsl:element>
      <xsl:element name="BtchBookg">
        placeholder
      </xsl:element>
      <xsl:element name="ReqdExctnDt">
        placeholder
      </xsl:element>
      <xsl:element name="DbtrAcct">
        <xsl:element name="Id">
          <xsl:element name="IBAN">
            placeholder
          </xsl:element>
        </xsl:element>
      </xsl:element>
      <xsl:element name="DbtrAgt">
        <xsl:element name="FinInstnId">
          <xsl:element name="BIC">
            placeholder
          </xsl:element>
        </xsl:element>
      </xsl:element>
    </xsl:element>
    <xsl:call-template name="epayment-transactioninfo"/>
  </xsl:template>
  
  <xsl:template name="epayment-transactioninfo">
    <xsl:element name="CdtTrfTxInf">
      <xsl:element name="PmtId">
        <xsl:element name="InstrId">
            placeholder
        </xsl:element>
        <xsl:element name="EndToEndId">
            placeholder
        </xsl:element>
      </xsl:element>
    </xsl:element>
    <xsl:element name="PmtTpInf">
      <xsl:element name="LclInstrm">
        <xsl:element name="Prtry">
            placeholder
        </xsl:element>
      </xsl:element>
    </xsl:element>
    <xsl:element name="Amt">
      <xsl:element name="InstdAmt">
        <xar:attribute name="Ccy">
            placeholder
        </xar:attribute>
              placeholder
      </xsl:element>
    </xsl:element>
    <xsl:element name="CdtrAcct">
      <xsl:element name="Id">
        <xsl:element name="Othr">
          <xsl:element name="Id">
            placeholder
          </xsl:element>
        </xsl:element>
      </xsl:element>
    </xsl:element>
    <xsl:element name="RmtInf">
      <xsl:element name="Strd">
        <xsl:element name="CdtrRefInf">
          <xsl:element name="Ref">
            placeholder
          </xsl:element>
        </xsl:element>
      </xsl:element>
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>