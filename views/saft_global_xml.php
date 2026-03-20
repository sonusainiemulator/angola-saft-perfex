<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<AuditFile xmlns="urn:OECD:StandardAuditFile-Tax:AO:1.01_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <Header>
    <AuditFileVersion>1.01_01</AuditFileVersion>
    <CompanyID><?php echo htmlspecialchars($COMPANY_VAT ?? ''); ?></CompanyID>
    <TaxRegistrationNumber><?php echo htmlspecialchars($COMPANY_VAT ?? ''); ?></TaxRegistrationNumber>
    <CompanyName><?php echo htmlspecialchars($COMPANY_NAME ?? ''); ?></CompanyName>
    <BusinessName><?php echo htmlspecialchars($COMPANY_NAME ?? ''); ?></BusinessName>
    <CompanyAddress>
      <AddressDetail><?php echo htmlspecialchars($COMPANY_ADDRESS ?? ''); ?></AddressDetail>
      <City><?php echo htmlspecialchars($COMPANY_CITY ?? ''); ?></City>
      <Country>AO</Country>
    </CompanyAddress>
    <FiscalYear><?php echo isset($INVOICE_DATE_FROM) ? date('Y', strtotime($INVOICE_DATE_FROM)) : date('Y'); ?></FiscalYear>
    <StartDate><?php echo htmlspecialchars($INVOICE_DATE_FROM ?? ''); ?></StartDate>
    <EndDate><?php echo htmlspecialchars($INVOICE_DATE_TO ?? ''); ?></EndDate>
    <CurrencyCode><?php echo htmlspecialchars($CURRENCY_CODE ?? ''); ?></CurrencyCode>
    <DateCreated><?php echo date('Y-m-d'); ?></DateCreated>
    <TaxEntity>Global</TaxEntity>
    <ProductCompanyID><?php echo htmlspecialchars($SOFTWARE_NAME ?? ''); ?></ProductCompanyID>
    <ProductID><?php echo htmlspecialchars($SOFTWARE_NAME ?? ''); ?>/<?php echo htmlspecialchars($CERTIFICATE_NO ?? ''); ?></ProductID>
    <ProductVersion>1.0</ProductVersion>
    <SoftwareCertificateNumber><?php echo htmlspecialchars($CERTIFICATE_NO ?? ''); ?></SoftwareCertificateNumber>
  </Header>
  <MasterFiles>
<?php if(isset($CUSTOMERS) && is_array($CUSTOMERS)): foreach($CUSTOMERS as $customer): ?>
    <Customer>
      <CustomerID><?php echo htmlspecialchars($customer['CUSTOMER_ID'] ?? ''); ?></CustomerID>
      <AccountID>Desconhecido</AccountID>
      <CustomerTaxID><?php echo htmlspecialchars($customer['CUSTOMER_VAT'] ?? ''); ?></CustomerTaxID>
      <CompanyName><?php echo htmlspecialchars($customer['CUSTOMER_NAME'] ?? ''); ?></CompanyName>
      <BillingAddress>
        <AddressDetail><?php echo htmlspecialchars($customer['CUSTOMER_ADDRESS'] ?? ''); ?></AddressDetail>
        <City><?php echo htmlspecialchars($customer['CUSTOMER_CITY'] ?? ''); ?></City>
        <Country>AO</Country>
      </BillingAddress>
      <SelfBillingIndicator>0</SelfBillingIndicator>
    </Customer>
<?php endforeach; endif; ?>
<?php if(isset($PRODUCTS) && is_array($PRODUCTS)): foreach($PRODUCTS as $product): ?>
    <Product>
      <ProductType>S</ProductType>
      <ProductCode><?php echo htmlspecialchars($product['ITEM_ID'] ?? ''); ?></ProductCode>
      <ProductGroup>Servicos</ProductGroup>
      <ProductDescription><?php echo htmlspecialchars($product['ITEM_DESCRIPTION'] ?? ''); ?></ProductDescription>
      <ProductNumberCode><?php echo htmlspecialchars($product['ITEM_ID'] ?? ''); ?></ProductNumberCode>
    </Product>
<?php endforeach; endif; ?>
    <TaxTable>
      <TaxTableEntry>
        <TaxType>IVA</TaxType>
        <TaxCountryRegion>AO</TaxCountryRegion>
        <TaxCode>NOR</TaxCode>
        <Description>Taxa Normal</Description>
        <TaxPercentage>14.00</TaxPercentage>
      </TaxTableEntry>
    </TaxTable>
  </MasterFiles>
  <SourceDocuments>
    <SalesInvoices>
      <NumberOfEntries><?php echo $TOTAL_ENTRIES ?? 0; ?></NumberOfEntries>
      <TotalDebit><?php echo number_format($TOTAL_DEBIT ?? 0, 2, '.', ''); ?></TotalDebit>
      <TotalCredit><?php echo number_format($TOTAL_CREDIT ?? 0, 2, '.', ''); ?></TotalCredit>
      <?php if(isset($INVOICES) && is_array($INVOICES)): foreach($INVOICES as $invoice): ?>
      <Invoice>
        <InvoiceNo><?php echo htmlspecialchars($invoice['INVOICE_NUMBER'] ?? ''); ?></InvoiceNo>
        <DocumentStatus>
          <InvoiceStatus>N</InvoiceStatus>
          <InvoiceStatusDate><?php echo htmlspecialchars($invoice['INVOICE_DATE'] ?? ''); ?>T00:00:00</InvoiceStatusDate>
          <SourceID><?php echo htmlspecialchars($invoice['STAFF_ID'] ?? ''); ?></SourceID>
          <SourceBilling>P</SourceBilling>
        </DocumentStatus>
        <Hash><?php echo htmlspecialchars($invoice['HASH'] ?? ''); ?></Hash>
        <HashControl><?php echo htmlspecialchars($invoice['HASH_CONTROL'] ?? ''); ?></HashControl>
        <Period>1</Period>
        <InvoiceDate><?php echo htmlspecialchars($invoice['INVOICE_DATE'] ?? ''); ?></InvoiceDate>
        <InvoiceType><?php echo htmlspecialchars($invoice['INVOICE_TYPE_CODE'] ?? 'FT'); ?></InvoiceType>
        <SpecialRegimes>
          <SelfBillingIndicator>0</SelfBillingIndicator>
          <CashVATSchemeIndicator>0</CashVATSchemeIndicator>
          <ThirdPartiesBillingIndicator>0</ThirdPartiesBillingIndicator>
        </SpecialRegimes>
        <SourceID><?php echo htmlspecialchars($invoice['STAFF_ID'] ?? ''); ?></SourceID>
        <SystemEntryDate><?php echo htmlspecialchars($invoice['INVOICE_DATE'] ?? ''); ?>T00:00:00</SystemEntryDate>
        <CustomerID><?php echo htmlspecialchars($invoice['CUSTOMER_ID'] ?? ''); ?></CustomerID>
        <?php if(isset($invoice['LINE_ITEMS']) && is_array($invoice['LINE_ITEMS'])): foreach($invoice['LINE_ITEMS'] as $line): ?>
        <Line>
          <LineNumber><?php echo htmlspecialchars($line['LINE_NUMBER'] ?? ''); ?></LineNumber>
          <ProductCode><?php echo htmlspecialchars($line['ITEM_ID'] ?? ''); ?></ProductCode>
          <ProductDescription><?php echo htmlspecialchars($line['ITEM_DESCRIPTION'] ?? ''); ?></ProductDescription>
          <Quantity><?php echo number_format($line['ITEM_QTY'] ?? 0, 2, '.', ''); ?></Quantity>
          <UnitOfMeasure>Unidade</UnitOfMeasure>
          <UnitPrice><?php echo number_format($line['ITEM_RATE'] ?? 0, 2, '.', ''); ?></UnitPrice>
          <TaxPointDate><?php echo htmlspecialchars($invoice['INVOICE_DATE'] ?? ''); ?></TaxPointDate>
          <Description><?php echo htmlspecialchars($line['ITEM_DESCRIPTION'] ?? ''); ?></Description>
<?php if(($invoice['INVOICE_TYPE_CODE'] ?? 'FT') == 'NC'): ?>
          <DebitAmount><?php echo number_format($line['ITEM_TOTAL'] ?? 0, 2, '.', ''); ?></DebitAmount>
          <CreditAmount>0.00</CreditAmount>
<?php else: ?>
          <DebitAmount>0.00</DebitAmount>
          <CreditAmount><?php echo number_format($line['ITEM_TOTAL'] ?? 0, 2, '.', ''); ?></CreditAmount>
<?php endif; ?>
          <Tax>
            <TaxType>IVA</TaxType>
            <TaxCountryRegion>AO</TaxCountryRegion>
            <TaxCode>NOR</TaxCode>
            <TaxPercentage>14.00</TaxPercentage>
          </Tax>
          <SettlementAmount>0.00</SettlementAmount>
        </Line>
        <?php endforeach; endif; ?>
        <DocumentTotals>
          <TaxPayable><?php echo number_format($invoice['INVOICE_TOTAL_TAX'] ?? 0, 2, '.', ''); ?></TaxPayable>
          <NetTotal><?php echo number_format($invoice['INVOICE_SUBTOTAL'] ?? 0, 2, '.', ''); ?></NetTotal>
          <GrossTotal><?php echo number_format($invoice['INVOICE_TOTAL'] ?? 0, 2, '.', ''); ?></GrossTotal>
        </DocumentTotals>
      </Invoice>
      <?php endforeach; endif; ?>
    </SalesInvoices>
  </SourceDocuments>
</AuditFile>
