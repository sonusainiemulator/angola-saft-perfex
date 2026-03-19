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

## 💎 Professional Services & Custom Development

**Looking for a custom Perfex CRM module or specialized integration?**  
We offer professional development services for Perfex CRM, including:
- **Custom Module Development**: Tailored solutions for your specific business needs.
- **Payment Gateway Integrations**: Secure and local payment methods.
- **Third-Party API Integrations**: Sync your CRM with any external tool.
- **Security & Compliance**: Regional tax compliance modules (like this one!).

### 🚀 [Hire Us / Request a Quote](mailto:your-email@example.com)

---

## 🤝 Contribution & Community
We welcome contributions from the community! Whether it's a bug fix, a new feature, or documentation improvement.
- **Check the [CONTRIBUTING.md](CONTRIBUTING.md) guide** to get started.
- **Join our community**: [Discord/Link] | [LinkedIn]

---

## 🆘 Help & Support
If you encounter any issues or have questions:
1. **GitHub Issues**: For bug reports and feature requests.
2. **Professional Support**: For immediate assistance and custom fixes, [contact us reached here](mailto:your-email@example.com).

---

## ❤️ Support Our Work
If this module has helped your business save time and stay compliant, consider supporting its continued development:
- **[Donate via PayPal](https://paypal.me/your-link)**
- **[Buy Me a Coffee](https://buymeacoffee.com/your-link)**

---

## 📄 License
This module is licensed under the MIT License.

