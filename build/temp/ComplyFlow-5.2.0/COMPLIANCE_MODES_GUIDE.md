# Compliance Modes - Detailed Impact Guide
**ComplyFlow v4.6.1**  
**What Happens When You Enable Each Compliance Framework**

---

## 📋 Overview

When you enable a compliance mode in ComplyFlow, the plugin automatically adjusts multiple components across your website to meet that regulation's requirements. This document explains exactly what changes when you check each compliance checkbox.

---

## 🌍 Compliance Framework Changes

### 1. 🇪🇺 GDPR (General Data Protection Regulation)
**Region:** European Union (27 countries)  
**Authority:** Data Protection Authorities (DPAs) in each member state

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Requires **explicit opt-in** before any non-essential cookies are set
- ✓ "Accept All" and "Reject All" buttons shown with equal prominence
- ✓ No cookies placed until user makes a choice (strict mode)
- ✓ Granular consent options for cookie categories (Analytics, Marketing, etc.)
- ✓ Easy consent withdrawal mechanism provided

**Privacy Policy Additions:**
- ✓ Legal basis for processing (Article 6 GDPR)
- ✓ Data subject rights section (Articles 15-22):
  - Right to Access
  - Right to Rectification
  - Right to Erasure ("Right to be Forgotten")
  - Right to Data Portability
  - Right to Restriction of Processing
  - Right to Object
- ✓ Data Protection Officer (DPO) contact information
- ✓ International data transfer mechanisms (SCCs, adequacy decisions)
- ✓ Automated decision-making disclosures

**User Rights Portal:**
- ✓ DSR (Data Subject Request) forms enabled
- ✓ Data access request form
- ✓ Data deletion request form
- ✓ Data portability download feature
- ✓ Consent withdrawal option

**Consent Logging:**
- ✓ Records consent timestamp
- ✓ IP address (anonymized - last octet removed)
- ✓ Consent categories (which cookies accepted)
- ✓ Consent version (tracks policy changes)
- ✓ Maintains audit trail for 3+ years

**Documentation:**
- ✓ References GDPR Articles in policies
- ✓ Links to relevant DPA websites
- ✓ Breach notification procedures (72-hour rule)
- ✓ Cookie policy with necessary/non-necessary distinction

---

### 2. 🇬🇧 UK GDPR (UK Data Protection Act 2018)
**Region:** United Kingdom  
**Authority:** Information Commissioner's Office (ICO)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Same strict opt-in requirements as EU GDPR
- ✓ UK-specific cookie consent language
- ✓ References UK DPA 2018 in notices

**Privacy Policy Additions:**
- ✓ UK GDPR compliance statement
- ✓ ICO contact information and complaint procedures
- ✓ Post-Brexit UK adequacy decisions documentation
- ✓ References to UK-approved Standard Contractual Clauses
- ✓ UK-specific lawful bases for processing
- ✓ Data Protection Fee registration notice (if applicable)

**User Rights Portal:**
- ✓ Same rights as GDPR with UK-specific forms
- ✓ ICO complaint lodging instructions
- ✓ UK Data Protection Officer designation

**Consent Logging:**
- ✓ Same as GDPR but marked with UK jurisdiction
- ✓ ICO-compliant record keeping

**Documentation:**
- ✓ References UK DPA 2018 sections
- ✓ ICO guidance links (ico.org.uk)
- ✓ UK-specific privacy notices

---

### 3. 🇺🇸 CCPA/CPRA (California Consumer Privacy Act)
**Region:** California, USA  
**Authority:** California Attorney General / California Privacy Protection Agency

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ "Do Not Sell My Personal Information" link prominently displayed
- ✓ Opt-out mechanism (not strict opt-in like GDPR)
- ✓ California-specific disclosure language
- ✓ Non-discrimination notice for privacy choices

**Privacy Policy Additions:**
- ✓ CCPA compliance statement and disclosures
- ✓ Categories of personal information collected
- ✓ Business purposes for collection
- ✓ Categories of sources from which data is collected
- ✓ Third parties with whom data is shared
- ✓ Sale of personal information disclosure (if applicable)
- ✓ Consumer rights section (CCPA-specific):
  - Right to Know (what data is collected)
  - Right to Delete
  - Right to Opt-Out of Sale
  - Right to Non-Discrimination
  - Right to Correct (under CPRA)
  - Right to Limit Use of Sensitive Personal Information (CPRA)

**User Rights Portal:**
- ✓ "Do Not Sell My Personal Information" submission form
- ✓ California resident verification process
- ✓ Data access request (45-day response time)
- ✓ Data deletion request with exceptions
- ✓ Authorized agent designation option

**Consent Logging:**
- ✓ Records opt-out choices
- ✓ Tracks "Do Not Sell" requests
- ✓ Maintains 24-month lookback records

**Documentation:**
- ✓ "Notice at Collection" disclosures
- ✓ Links to California AG privacy resources
- ✓ Sensitive personal information categories
- ✓ Financial incentive disclosures (if applicable)

---

### 4. 🇧🇷 LGPD (Lei Geral de Proteção de Dados)
**Region:** Brazil  
**Authority:** Autoridade Nacional de Proteção de Dados (ANPD)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Explicit consent required for data processing
- ✓ Clear consent withdrawal mechanism
- ✓ Portuguese language option available
- ✓ Purpose-specific consent requests

**Privacy Policy Additions:**
- ✓ LGPD compliance statement
- ✓ Legal bases for processing (10 bases under LGPD)
- ✓ Data Protection Officer (Encarregado) designation
- ✓ ANPD contact and complaint procedures
- ✓ International data transfer safeguards
- ✓ Data sharing record maintenance
- ✓ Sensitive data processing disclosures

**User Rights Portal:**
- ✓ Data access requests (Titular rights)
- ✓ Correction of incomplete/inaccurate data
- ✓ Anonymization, blocking, or deletion requests
- ✓ Data portability to another provider
- ✓ Revocation of consent
- ✓ Information about public/private entities receiving data

**Consent Logging:**
- ✓ Records consent with legal basis
- ✓ Purpose-specific logging
- ✓ Consent version tracking

**Documentation:**
- ✓ References LGPD articles
- ✓ Links to ANPD website
- ✓ Encarregado (DPO) contact details
- ✓ Data processing registry information

---

### 5. 🇨🇦 PIPEDA (Personal Information Protection and Electronic Documents Act)
**Region:** Canada (Federal)  
**Authority:** Office of the Privacy Commissioner of Canada

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ **Meaningful consent** required (clear, understandable language)
- ✓ Consent forms easy to read and understand
- ✓ Separate consent requests for different purposes
- ✓ Consequences of consent/refusal explained
- ✓ Easy consent withdrawal

**Privacy Policy Additions:**
- ✓ PIPEDA compliance statement
- ✓ 10 Fair Information Principles:
  1. Accountability
  2. Identifying Purposes
  3. Consent
  4. Limiting Collection
  5. Limiting Use, Disclosure, and Retention
  6. Accuracy
  7. Safeguards
  8. Openness
  9. Individual Access
  10. Challenging Compliance
- ✓ Privacy Officer designation
- ✓ Breach notification procedures
- ✓ Office of the Privacy Commissioner contact

**User Rights Portal:**
- ✓ Access to personal information requests
- ✓ Correction of inaccurate information
- ✓ Consent withdrawal mechanism
- ✓ Complaint filing with Privacy Commissioner

**Consent Logging:**
- ✓ Records meaningful consent (with purpose disclosure)
- ✓ Tracks consent withdrawal
- ✓ Documents disclosure to third parties

**Documentation:**
- ✓ References PIPEDA sections
- ✓ Links to priv.gc.ca resources
- ✓ Breach reporting thresholds (real risk of significant harm)
- ✓ Privacy Officer contact information

---

### 6. 🇸🇬 PDPA (Personal Data Protection Act) - Singapore
**Region:** Singapore  
**Authority:** Personal Data Protection Commission (PDPC)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Consent obtained before collection/use/disclosure
- ✓ Purpose notification provided
- ✓ Voluntary consent mechanism
- ✓ Withdrawal option clearly stated

**Privacy Policy Additions:**
- ✓ PDPA compliance statement
- ✓ 9 Data Protection Obligations:
  1. Consent Obligation
  2. Purpose Limitation Obligation
  3. Notification Obligation
  4. Access and Correction Obligation
  5. Accuracy Obligation
  6. Protection Obligation
  7. Retention Limitation Obligation
  8. Transfer Limitation Obligation
  9. Openness Obligation
- ✓ Data Protection Officer contact
- ✓ Do Not Call (DNC) Registry compliance
- ✓ Cross-border transfer safeguards

**User Rights Portal:**
- ✓ Access requests (how data used/disclosed in past year)
- ✓ Correction of errors or omissions
- ✓ Withdrawal of consent
- ✓ PDPC complaint filing

**Consent Logging:**
- ✓ Records purpose-specific consent
- ✓ Tracks how data has been used
- ✓ Documents third-party disclosures

**Documentation:**
- ✓ References PDPA sections
- ✓ Links to pdpc.gov.sg
- ✓ DNC Registry compliance notices
- ✓ Data breach notification procedures

---

### 7. 🇹🇭 PDPA (Personal Data Protection Act) - Thailand
**Region:** Thailand  
**Authority:** Personal Data Protection Committee (Ministry of Digital Economy and Society)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Consent required for processing
- ✓ Thai language option available
- ✓ Clear purpose disclosure
- ✓ Easy withdrawal mechanism

**Privacy Policy Additions:**
- ✓ Thailand PDPA compliance statement
- ✓ 6 Legal bases for processing:
  1. Consent
  2. Contract Performance
  3. Legal Obligation
  4. Vital Interests
  5. Public Interest
  6. Legitimate Interests
- ✓ 8 Data Subject Rights:
  1. Right to Withdraw Consent
  2. Right to Access
  3. Right to Data Portability
  4. Right to Object
  5. Right to Erasure
  6. Right to Restriction
  7. Right to Rectification
  8. Right to Complaint
- ✓ Data Protection Officer (DPO) designation
- ✓ Cross-border transfer safeguards

**User Rights Portal:**
- ✓ All 8 data subject rights forms
- ✓ Data portability (machine-readable format)
- ✓ Direct marketing objection
- ✓ PDPC complaint submission

**Consent Logging:**
- ✓ Records legal basis for processing
- ✓ Tracks consent versions
- ✓ Documents cross-border transfers

**Documentation:**
- ✓ References Thailand PDPA sections
- ✓ DPO contact information
- ✓ Breach notification (within prescribed time)
- ✓ PDPC contact details

---

### 8. 🇯🇵 APPI (Act on the Protection of Personal Information)
**Region:** Japan  
**Authority:** Personal Information Protection Commission (PPC)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Purpose specification before collection
- ✓ Consent for special care-required personal information
- ✓ Japanese language support
- ✓ Clear opt-out mechanisms

**Privacy Policy Additions:**
- ✓ APPI compliance statement
- ✓ Purpose of use specification
- ✓ Special care-required personal information (sensitive data):
  - Race, creed, social status
  - Medical history
  - Criminal record and related information
- ✓ Security management measures
- ✓ Third-party provision rules
- ✓ Cross-border transfer requirements
- ✓ Anonymized information procedures

**User Rights Portal:**
- ✓ Disclosure requests (personal data access)
- ✓ Correction requests (inaccurate data)
- ✓ Suspension of use/erasure requests
- ✓ Suspension of third-party provision
- ✓ PPC complaint filing

**Consent Logging:**
- ✓ Records purpose of use
- ✓ Tracks special care-required data separately
- ✓ Documents third-party provisions
- ✓ Maintains retention personal data records

**Documentation:**
- ✓ References APPI sections
- ✓ Links to ppc.go.jp/en/
- ✓ Publicly announces retained personal data matters
- ✓ Anonymized information handling procedures

---

### 9. 🇿🇦 POPIA (Protection of Personal Information Act)
**Region:** South Africa  
**Authority:** Information Regulator (South Africa)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Consent or other lawful processing condition required
- ✓ Purpose disclosure at collection
- ✓ Information about consequences of refusal
- ✓ Objection to processing option

**Privacy Policy Additions:**
- ✓ POPIA compliance statement
- ✓ 8 Conditions for Lawful Processing:
  1. Accountability
  2. Processing Limitation
  3. Purpose Specification
  4. Further Processing Limitation
  5. Information Quality
  6. Openness
  7. Security Safeguards
  8. Data Subject Participation
- ✓ Information Officer designation
- ✓ Special personal information handling
- ✓ Direct marketing opt-out procedures
- ✓ Cross-border transfer requirements

**User Rights Portal:**
- ✓ Confirmation of personal information held
- ✓ Access to personal information
- ✓ Correction/destruction/deletion requests
- ✓ Objection to processing
- ✓ Information Regulator complaint filing

**Consent Logging:**
- ✓ Records lawful processing condition
- ✓ Tracks purpose of processing
- ✓ Documents special personal information
- ✓ Direct marketing consent/objection

**Documentation:**
- ✓ References POPIA sections
- ✓ Links to justice.gov.za/inforeg/
- ✓ Information Officer contact details
- ✓ Security safeguards documentation

---

### 10. 🇹🇷 KVKK (Kişisel Verilerin Korunması Kanunu)
**Region:** Turkey  
**Authority:** Personal Data Protection Authority (Kişisel Verileri Koruma Kurumu)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Explicit consent for processing
- ✓ Turkish language support
- ✓ Clear information obligation
- ✓ Special categories consent (if applicable)

**Privacy Policy Additions:**
- ✓ KVKK compliance statement
- ✓ 5 General Principles:
  1. Compliance with Law and Good Faith
  2. Accuracy and Currency
  3. Processing for Specific, Explicit, Legitimate Purposes
  4. Relevance and Limitation
  5. Retention for Required Period Only
- ✓ Legal grounds for processing (6 bases)
- ✓ Special categories of personal data protection
- ✓ VERBİS (Data Controllers Registry) registration notice
- ✓ Information obligation details
- ✓ Cross-border transfer rules

**User Rights Portal:**
- ✓ Learn whether data is being processed
- ✓ Request information if processed
- ✓ Learn purpose and proper use
- ✓ Know third parties receiving data
- ✓ Request rectification
- ✓ Request deletion/destruction
- ✓ Request notification to third parties
- ✓ Object to automated processing
- ✓ Claim compensation for damages

**Consent Logging:**
- ✓ Records explicit consent
- ✓ Tracks special categories separately
- ✓ Documents cross-border transfers
- ✓ Maintains 30-day response records

**Documentation:**
- ✓ References KVKK articles
- ✓ Links to kvkk.gov.tr
- ✓ VERBİS registration information
- ✓ Application form and procedures
- ✓ 30-day response time commitment
- ✓ Data breach notification procedures

---

### 11. 🇸🇦 PDPL (Personal Data Protection Law)
**Region:** Saudi Arabia  
**Authority:** Saudi Data & Artificial Intelligence Authority (SDAIA)

#### ✅ What Changes When Enabled:

**Consent Banner Behavior:**
- ✓ Consent or other legal basis required
- ✓ Arabic language support available
- ✓ Purpose specification required
- ✓ Withdrawal mechanism provided

**Privacy Policy Additions:**
- ✓ PDPL compliance statement
- ✓ 7 Processing Principles:
  1. Lawfulness and Fairness
  2. Purpose Limitation
  3. Data Minimization
  4. Accuracy
  5. Storage Limitation
  6. Integrity and Confidentiality
  7. Accountability
- ✓ Legal basis for processing (6 bases)
- ✓ Sensitive personal data protections
- ✓ Data Protection Officer (DPO) designation
- ✓ Data Protection Impact Assessment (DPIA) procedures
- ✓ Children's data protection (under 18)
- ✓ International transfer safeguards

**User Rights Portal:**
- ✓ Right to Information
- ✓ Right of Access
- ✓ Right to Rectification
- ✓ Right to Erasure
- ✓ Right to Restriction
- ✓ Right to Data Portability
- ✓ Right to Object
- ✓ Right to Withdraw Consent
- ✓ SDAIA complaint lodging

**Consent Logging:**
- ✓ Records legal basis for processing
- ✓ Tracks consent for sensitive data
- ✓ Documents DPIA outcomes
- ✓ Maintains breach notification records (72-hour rule)

**Documentation:**
- ✓ References PDPL articles
- ✓ Links to sdaia.gov.sa
- ✓ DPO contact information
- ✓ DPIA trigger conditions
- ✓ Children's consent procedures
- ✓ Breach notification procedures (within 72 hours)

---

## 🔄 How Compliance Modes Work Together

### Multiple Frameworks Enabled
When you enable **multiple compliance modes simultaneously**, ComplyFlow:

✅ **Combines Requirements:** Takes the strictest requirement from any enabled framework
✅ **Merges Policies:** Includes all relevant compliance sections in generated policies
✅ **Maximizes Rights:** Provides the broadest set of user rights across all frameworks
✅ **Comprehensive Logging:** Records all consent types and purposes

**Example:** If both GDPR and CCPA are enabled:
- Uses GDPR's strict opt-in for EU visitors
- Shows "Do Not Sell" for California visitors
- Privacy policy includes both GDPR and CCPA sections
- User rights portal supports both sets of rights
- Consent logs track both GDPR consent and CCPA opt-outs

### Geo-Targeting (Future Feature)
Once implemented, ComplyFlow will:
- Detect visitor location via IP geolocation
- Automatically apply relevant compliance requirements
- Show region-appropriate consent banners
- Only log applicable compliance frameworks

---

## 📊 Technical Implementation Details

### Where Compliance Settings Are Used

#### 1. **Frontend (Visitor-Facing)**
- **Consent Banner** (`includes/Modules/Consent/ConsentBanner.php`)
  - Reads enabled compliance modes
  - Adjusts banner text and buttons accordingly
  - Implements opt-in vs opt-out logic

- **Cookie Blocking** (`includes/Modules/Consent/ScriptBlocker.php`)
  - Blocks scripts based on strictest enabled framework
  - GDPR/UK GDPR = block until explicit consent
  - CCPA = allow until opt-out

#### 2. **Backend (Admin/Processing)**
- **Policy Generator** (`includes/Modules/Legal/PolicyGenerator.php`)
  - Checks which compliance modes are enabled
  - Includes corresponding template snippets
  - References: `templates/policies/snippets/[framework]-compliance.php`

- **Consent Logger** (`includes/Modules/Consent/ConsentLogger.php`)
  - Records enabled frameworks at time of consent
  - Stores consent decisions in database
  - Table: `wp_complyflow_consent_logs`

- **DSR Handler** (`includes/Modules/DSR/DSRHandler.php`)
  - Processes data subject requests
  - Applies strictest response timeline from enabled frameworks
  - GDPR = 30 days, CCPA = 45 days, KVKK = 30 days

#### 3. **Database Storage**
Settings are stored in `wp_options` table:
```
consent_gdpr_enabled = 1 or 0
consent_uk_gdpr_enabled = 1 or 0
consent_ccpa_enabled = 1 or 0
consent_lgpd_enabled = 1 or 0
consent_pipeda_enabled = 1 or 0
consent_pdpa_sg_enabled = 1 or 0
consent_pdpa_th_enabled = 1 or 0
consent_appi_enabled = 1 or 0
consent_popia_enabled = 1 or 0
consent_kvkk_enabled = 1 or 0
consent_pdpl_enabled = 1 or 0
```

---

## 🎯 Decision Matrix: Which Frameworks to Enable

### ✅ Enable If:

**GDPR:**
- You have users in any EU country
- You target EU markets
- You want strictest privacy standards globally

**UK GDPR:**
- You have users in the United Kingdom
- You process UK resident data
- You have UK-based operations

**CCPA:**
- You have users in California
- Your business meets CCPA thresholds (revenue, data volume)
- You sell to California consumers

**LGPD:**
- You have users in Brazil
- You offer products/services to Brazilian market
- You process Brazilian resident data

**PIPEDA:**
- You have users in Canada (outside Quebec/BC/Alberta)
- You conduct commercial activities in Canada
- Federal privacy law applies to your business

**PDPA (Singapore):**
- You have users in Singapore
- You operate in Singapore
- You collect data of Singapore residents

**PDPA (Thailand):**
- You have users in Thailand
- You target Thai market
- You process Thai resident data

**APPI:**
- You have users in Japan
- You operate in Japan
- You process Japanese resident data

**POPIA:**
- You have users in South Africa
- You conduct business in South Africa
- You process SA resident data

**KVKK:**
- You have users in Turkey
- You offer services to Turkish market
- You process Turkish resident data

**PDPL:**
- You have users in Saudi Arabia
- You operate in Saudi Arabia or target Saudi market
- You process Saudi resident data

### 💡 Best Practice Recommendations

1. **Start with GDPR** - It's the strictest standard and provides good baseline compliance
2. **Enable all regions where you have users** - Better over-compliant than under-compliant
3. **Review annually** - Laws change, new frameworks emerge
4. **Document decisions** - Keep records of why certain frameworks were enabled/disabled
5. **Consult legal counsel** - This guide aids implementation but doesn't replace legal advice

---

## ⚠️ Important Disclaimers

### What ComplyFlow Does:
✅ Provides technical tools for compliance  
✅ Generates baseline policy content  
✅ Logs consent properly  
✅ Implements user rights portals  
✅ References official regulatory authorities  

### What ComplyFlow Does NOT Do:
❌ Constitute legal advice  
❌ Guarantee full legal compliance  
❌ Replace consultation with qualified attorneys  
❌ Automatically update for law changes  
❌ Handle all edge cases or special situations  

### Your Responsibilities:
1. **Customize policies** to match your specific data practices
2. **Review generated content** with legal counsel
3. **Respond to user requests** within required timeframes
4. **Maintain documentation** of compliance efforts
5. **Monitor regulatory changes** and update accordingly
6. **Train staff** on privacy obligations
7. **Conduct regular audits** of data processing activities

---

## 📚 Further Resources

### Official Regulatory Websites:
- **GDPR:** https://gdpr.eu/
- **UK GDPR:** https://ico.org.uk/
- **CCPA:** https://oag.ca.gov/privacy/ccpa
- **LGPD:** https://www.gov.br/cidadania/pt-br/acesso-a-informacao/lgpd
- **PIPEDA:** https://www.priv.gc.ca/
- **PDPA (SG):** https://www.pdpc.gov.sg/
- **APPI:** https://www.ppc.go.jp/en/
- **POPIA:** https://www.justice.gov.za/inforeg/
- **KVKK:** https://www.kvkk.gov.tr/
- **PDPL:** https://sdaia.gov.sa/

### ComplyFlow Documentation:
- Installation Guide: `INSTALLATION.md`
- User Guide: `docs/USER-GUIDE.md`
- Global Compliance Audit: `GLOBAL_COMPLIANCE_AUDIT.md`
- API Reference: `docs/API-REFERENCE.md`

---

**Last Updated:** November 25, 2025  
**Version:** ComplyFlow 4.6.1  
**Document Type:** Administrator Guide
