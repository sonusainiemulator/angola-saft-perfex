# 🇦🇴 Angola E-Invoice (SAF-T AO) for Perfex CRM

![Perfex Version](https://img.shields.io/badge/Perfex_CRM-v3.4.1-blue?style=for-the-badge&logo=php)
![AGT Compliance](https://img.shields.io/badge/AGT-Compliance-green?style=for-the-badge&logo=checkmarx)
![License](https://img.shields.io/badge/License-MIT-orange?style=for-the-badge)

A powerful, secure, and fully compliant electronic invoicing module for Perfex CRM, designed to meet the mandatory requirements of the **General Tax Administration (AGT - Administração Geral Tributária)** of Angola.

---

## 🔥 Key Features

### 🛡️ Digital Security
- **Asymmetric Encryption**: Full RSA-SHA1 signing for all tax-relevant documents.
- **Hash Chaining**: Secure sequential linking for audit integrity (prevents post-hoc document modification).
- **Key Management**: Integrated storage and rotation of RSA Private & Public keys.

### 📤 Compliance & Reporting
- **SAF-T AO 1.01 XML**: Monthly global summary generation with full audit details.
- **Real-time API (2026)**: Automatic JSON submission to the AGT REST API gateway for government-cleared invoicing.
- **Auto-Formatting**: Compliant numbering for Invoices (`FT`) and Credit Notes (`NC`).

### ⚡ Seamless Integration
- **Perfex 3.4.1 Support**: Built specifically for modern Perfex installations with updated sidebars, settings, and permissions.
- **Mustache Engine**: Leverages the flexible `einvoice` baseline for highly customizable XML/JSON templates.
- **Automatic Submission**: Asynchronous hooks for document signing and API reporting on creation.

---

## 🛠️ Technical Stack
- **Languages**: PHP 8.x
- **Namespaces**: PSR-4 (`AngolaSaft\`)
- **Encryption**: OpenSSL (RSA-SHA1)
- **Reporting**: SAF-T AO 1.01 (XSD-compliant)
- **Templates**: Mustache-based Data Mapping

---

## 📦 Installation

1. **Clone the repository** into your Perfex CRM modules directory:
   ```bash
   cd application/modules
   git clone https://github.com/your-repo/angola_saft.git
   ```

2. **Activate the module**:
   Navigate to **Setup > Modules** in your Perfex Admin dashboard and click **Activate** on "Angola E-Invoice".

---

## ⚙️ Configuration

1. **Software Certification**: Enter your AGT certification number in the module settings.
2. **Key Setup**:
   - Go to **Settings > Finance > Angola E-Invoice**.
   - Paste your **RSA Private Key** and **RSA Public Key** (PEM format).
3. **API Integration**:
   - Provide the **AGT Portal Endpoint** and **API Bearer Token**.

---

## 📄 Global Export
Access the global SAF-T AO export utility under **Utilities > Angola SAF-T Export**. You can generate monthly XML audit files for a custom date range.

---

## 🤝 Contributing
Contributions are welcome! If you have suggestions for new features or find a bug, please open an issue or submit a pull request.

## 📄 License
This module is licensed under the MIT License.
