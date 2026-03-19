# 🇦🇴 Angola E-Invoice (SAF-T AO) - Key Features

Comprehensive compliance module for Angola's **Administração Geral Tributária (AGT)** mandatory electronic invoicing system.

## 🚀 Core Functionalities

### 1. **Automatic Digital Signing (RSA-SHA1)**
- Every Invoice and Credit Note created in Perfex CRM is automatically signed upon save.
- Leverages **RSA asymmetric encryption** (Private/Public key pair) as mandated by AGT.
- Compliant with **Standard Audit File for Tax (SAF-T AO 1.01)** signature requirements.

### 2. **Sequential Hash Chaining (Blockchain Integrity)**
- Implements a secure chaining mechanism where each document's payload includes the **hash of the previous document**.
- Ensures that documents cannot be deleted or modified post-hoc without breaking the audit chain.
- Automatic retrieval of the last valid hash from the `tblsaft_ao_hashes` table.

### 3. **Real-time AGT API Reporting (2026 Mandate)**
- Integrated **REST API client** for near real-time submission of JSON documents to the AGT portal.
- Handles standard API response patterns (`20x` Success, `4xx` Validation Errors).
- Automatic logging of API submission status in the Perfex CRM Activity Log.

### 4. **Global SAF-T AO 1.01 Export**
- Dedicated utility for generating the **standardized XML audit file** for tax authorities.
- Date range filtering (Monthly/Yearly) for bulk document collection.
- Includes mandatory Header information (Software Certification No, Company VAT, Version).

## 🛠 Technical Highlights
- **Perfex 3.4.1 Ready**: Fully optimized for the latest sidebar, settings, and permission systems.
- **Mustache-based Templates**: Flexible XML/JSON generation using the existing `einvoice` module architecture.
- **Secure Key Management**: Options for secure storage of RSA keys (Private/Public) directly in settings.

## 📋 Compliance Details
- **Audit File Version**: `1.01_01` (Latest SAF-T AO).
- **Invoicing Codes**: Auto-formatting for `FT` (Invoices) and `NC` (Credit Notes) prefixed by Year and ID.
- **Certification Number**: Dedicated field for the AGT software certification ID (e.g., `xxx/AGT/2026`).
