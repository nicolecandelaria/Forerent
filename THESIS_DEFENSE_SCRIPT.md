# CITISPACE FORERENT - Thesis Defense Demonstration Script

> **Instructions**: Read this script while demonstrating the system. Text in **[brackets]** are actions you perform on screen. Text in **(parentheses)** are notes to yourself — do not read aloud.

---

## OPENING

Good day, everyone. Today I will be demonstrating **Citispace Forerent**, a web-based rental property management system designed for dormitory-style properties. The system serves three types of users — the **Property Owner or Landlord**, the **Property Manager**, and the **Tenant** — each with their own dashboard and set of features tailored to their role.

The system is built using **Laravel** with **Livewire** for real-time, reactive user interfaces, and it integrates a **Python-based machine learning API** using FastAPI and Scikit-learn for intelligent pricing and forecasting. It also follows Philippine landlord-tenant law, specifically **Republic Act 9653**, for deposit interest calculations and tenant protections.

Let me walk you through the entire system, starting from how users access it.

---

## SECTION 1: AUTHENTICATION AND ROLE-BASED ACCESS

**[Open the login page in the browser]**

This is the login page of Citispace Forerent. The system does not have a public registration feature — this is intentional. Only authorized administrators can create user accounts, which prevents unauthorized access to the platform. Users log in using their **email and password**.

The system also requires the user to **accept the Terms and Conditions** before logging in.

There are three roles in the system: **Landlord**, **Manager**, and **Tenant**. Each role is protected by a custom **role-based middleware**. When a user logs in, the system checks their role and **automatically redirects them to the correct dashboard**. A tenant cannot access the landlord's pages, and a landlord cannot access the tenant's pages. If someone tries to access a page they are not authorized for, the system returns a **403 Forbidden error**.

The login is also **rate-limited** — a user can only attempt to log in **five times per minute**. After that, they are temporarily blocked. This protects the system from brute-force attacks.

---

## SECTION 2: LANDLORD / PROPERTY OWNER FLOW

**[Log in as landlord@example.com / password]**

I am now logging in as the **Property Owner**, Liam Landlord. As you can see, after logging in, the system redirected me to the **Landlord Dashboard**.

### 2.1 Dashboard Overview

**[Point to the greeting banner at the top]**

At the top, we have a **greeting banner** that shows the owner's name and today's date.

**[Point to the KPI cards]**

Below that, we have four **key performance indicator cards**:

- **Total Units** — the total number of units across all properties
- **Fully Booked Units** — units where every bed has an active lease
- **Available Units** — units where at least one bed is still open
- **Vacant Units** — units with no active leases at all

Each card also shows a **comparison with the previous month**, so the owner can quickly see if occupancy is improving or declining.

**[Point to the Pending Contracts widget]**

Next, we have the **Contracts Awaiting Signature** section. This shows contracts that are still in progress and need signatures. Each contract shows three indicators — **O, M, and T** — which stand for **Owner, Manager, and Tenant**. The indicator lights up green when that party has already signed, and a counter shows how many out of three signatures are complete. If the owner still needs to sign, a **"Sign Now"** button appears.

**[Point to the Financial Overview section]**

Below that is the **Financial Overview**. On the left, we have a **Revenue vs. Expenses chart** — this is a 12-month bar chart showing monthly **income from rent** compared to **expenses from maintenance**. On the right is a **Rent Collection summary** that shows the **total collected rent versus uncollected rent** with percentages, so the owner can see their collection rate at a glance.

The owner also has access to **Announcements** and a **Calendar Widget** for tracking events.

### 2.2 Property Management

**[Navigate to the Property page]**

This is the **Property Management** page. It lists all properties owned by this landlord. The owner can **add a new property** by clicking the add button.

**[Click Add Property — or explain the form]**

When adding a property, the owner fills in the **building name**, **address**, and **description**. They are required to upload at least **one property photo** — each photo has a maximum file size of **10 megabytes**. They also upload important **legal documents** such as the Business Permit, BIR 2303, Barangay Clearance, Occupancy Permit, and others. These documents are required when creating a property for the first time.

If the owner tries to submit without filling in required fields — for example, leaving the building name blank or not uploading a photo — the system will show a **validation error** and prevent submission. Files larger than 10 megabytes are also rejected.

### 2.3 Unit Management and ML-Based Price Prediction

**[Navigate to Add Unit or explain the 3-step wizard]**

Now let me show you one of the key features of the system — **Unit Creation with Machine Learning Price Prediction**.

Adding a unit uses a **three-step wizard**.

**Step 1** is **Unit Details**: the owner selects which property this unit belongs to, the **floor number**, the **occupant type** — which can be Male, Female, or Co-ed — the **living area in square feet**, the **bed type** — either Single or Bunk — and the **room capacity**, which is how many beds the unit can hold.

**Step 2** is **Model Amenities**: the owner selects which amenities this unit offers. There are **16 amenities** available, including Free WiFi, Hot and Cold Shower, Air Conditioning, Refrigerator, Washing Machine, and others. Based on how many amenities are selected, the system **automatically calculates the furnishing level** — if no amenities are selected, it is classified as "Bare"; if all are selected, it is "Fully Furnished"; otherwise it is "Semi-furnished."

**Step 3** is **Review and Predict**: this is where the machine learning model comes in. The system takes all the information entered — the living area, floor, bed type, room capacity, furnishing level, and all 16 amenity flags — and sends it to a **Python FastAPI service** running a **cluster-based Linear Regression model**. The model was trained on dormitory pricing data, and it uses **Agglomerative Clustering with Ward linkage** to segment similar units, then applies a **per-cluster Linear Regression** to predict the optimal price.

The predicted price appears on screen in Philippine Pesos. The owner can **accept the prediction as the listing price**, or they can **manually override it** with their own price.

Now, what happens if the machine learning service is unavailable? The system handles this gracefully — if the API times out after 5 seconds or returns an error, it **falls back to a reasonable default range** so the system never crashes. This is an example of **graceful degradation** in our architecture.

### 2.4 Manager Assignment

**[Navigate to Manager Details page]**

The landlord can also manage **Property Managers** from this page. When adding a manager, the landlord fills in the manager's personal details — name, email, and contact number — and then assigns them to specific **units**. The assignment is done by selecting a building, then a floor, and then choosing which units on that floor the manager will handle.

There is a **maximum of 10 units per manager** to ensure manageable workloads. Only unassigned units are shown in the selection. Once saved, the manager receives a **welcome email** with their login credentials.

### 2.5 Revenue Reports and Forecasting

**[Navigate to Revenue page]**

This section provides **analytics and forecasting** tools.

The **Inflow and Outflow chart** shows a 12-month comparison of income from rent payments versus expenses from maintenance.

The **Maintenance Cost Breakdown** is a pie chart showing how maintenance costs are distributed across categories — Plumbing, Electrical, Structural, Appliance, and Pest Control. This can be filtered by month or by the full year.

We also have **Revenue Forecasting** and **Maintenance Forecasting** — these use a separate **Python machine learning API** that analyzes historical transaction data and predicts future revenue and maintenance costs. The forecasts include monthly predictions, total annual projections, and seasonal factors. If the forecasting API is unavailable, the system falls back to **historical averages** and flags the result as a fallback so the owner knows the data is estimated.

**[Log out]**

That covers the Landlord's capabilities. Now let me switch to the Manager role.

---

## SECTION 3: MANAGER FLOW

**[Log in as manager@example.com / password]**

I am now logged in as **Marcus Manager**. The system has redirected me to the **Manager Dashboard**.

### 3.1 Manager Dashboard

**[Point to the dashboard elements]**

The Manager's dashboard shows a **greeting banner**, an **announcements section** for messages from the landlord, a **calendar widget**, and **Maintenance Statistics** — showing how many pending, ongoing, and completed maintenance requests exist. This gives the manager a quick overview of their workload.

### 3.2 Tenant Management

**[Navigate to Tenant Management page]**

This is the **Tenant Management** page. It shows a list of tenants with **three tabs**: Current tenants, Moving Out tenants, and Past tenants. The manager can **search by name** and **sort by newest or oldest**.

**[Click on a tenant or explain the Add Tenant flow]**

When adding a new tenant, the manager fills in their **personal information** — first name, last name, email, contact number, gender, permanent address — along with their **government ID details** and **emergency contact information**. The manager then assigns the tenant to a specific **bed in a managed unit** and sets the **lease terms**: start date, end date, lease term in months, shift — Morning or Night — the contract rate, security deposit, advance payment, and the monthly due date.

Once saved, the system creates a **lease record** in draft status, ready for the contract signing process.

### 3.3 Contract and E-Signature Workflow

**[Navigate to or explain the Contract panel]**

The contract workflow is a **three-party e-signature process**. When a lease is created, the contract starts in **Draft** status. The manager can initiate the signing process, which moves the contract through several stages:

1. The **Manager signs first** — the status becomes "Pending Owner"
2. The **Owner signs** — the status becomes "Pending Tenant"
3. The **Tenant signs** — the status becomes "Executed"

Each signature records three pieces of information: the **signature image**, the **timestamp** of when it was signed, and the **IP address** of the signer. This creates a complete **audit trail** for legal accountability.

There is also a **Contract Audit Log** that records every change made to any contract — who changed it, what was changed, the old value, the new value, and when. This provides full transparency and accountability.

If any party tries to sign without first accepting the terms and conditions, the system will **block the signature** and show an error.

### 3.4 Move-In Inspection

**[Explain the inspection form]**

Before a tenant fully moves in, the manager conducts a **Move-In Inspection**. This is a checklist where the manager records every item in the unit — its **name, condition** (good, damaged, or missing), **quantity**, and any **remarks**. The tenant then **confirms receipt** of these items. This is important because at move-out, the system will **compare the move-in and move-out inspections** to determine if there are damages or missing items that should be deducted from the security deposit.

If the tenant disagrees with any item's recorded condition, they can raise a **dispute**, and the manager can provide a resolution.

### 3.5 Billing Management

**[Navigate to Payment/Billing page]**

This is where the manager handles **billing**. The system generates **monthly billings** for each tenant. Each billing contains **itemized charges**:

- **Rent** — the recurring monthly rate from the contract
- **Electricity Share** — the tenant's share of the unit's electricity bill
- **Water Share** — the tenant's share of the unit's water bill
- **Short-term Premium** — an additional charge if the lease term is less than 6 months
- **Late Payment Fee** — a percentage-based penalty calculated daily for overdue payments
- **Violation Fees** — any fines from violations

The manager also enters **Utility Bills** for each unit. When entering a utility bill, the manager selects the building, unit, utility type — electricity or water — the billing period, and the total amount. The system then **automatically calculates each tenant's share**. If a tenant moved in mid-month, their share is **prorated** based on the number of days they occupied the unit. This ensures fair and accurate billing.

Each billing has a **status**: Unpaid, Overdue, or Paid. The system also tracks **previous balances** — if a tenant did not pay last month, that unpaid amount carries over to the next billing period.

### 3.6 Payment Request Verification

**[Navigate to Payment Requests]**

When a tenant submits a payment, it appears here as a **Payment Request** with a status of **Pending**. The manager can see the payment details — the amount, payment method, reference number, and the **proof of payment image** the tenant uploaded.

The manager has two options:

1. **Confirm** the payment — this sets the billing status to "Paid", creates a **Transaction record** in the system, generates a **transaction reference number**, and sends a **notification to the tenant** confirming that their payment was received.
2. **Reject** the payment — the manager selects a **rejection reason**, and the tenant receives a notification explaining why. The tenant can then **resubmit** with a corrected payment or new proof.

This verification process ensures that all payments are legitimate before being recorded in the system.

### 3.7 Violation Management

**[Navigate to Violations page]**

The manager can issue **violations** against tenants. When creating a violation, the manager selects the tenant, chooses a **category** — such as Noise Violation — selects the **severity level** (minor, major, or serious), writes a **description** (minimum 10 characters, maximum 2,000), sets the **violation date** (which cannot be in the future), and can upload up to **3 evidence photos** (maximum 5 megabytes each).

Here is where the **Violation Escalation Service** comes in. The system automatically determines the penalty based on the tenant's **offense history** and the violation's severity:

- **1st offense** — the tenant receives a **Written Warning**. No fine is charged.
- **2nd offense** — the tenant is charged a **Fine**. The fine amount is configured in the property settings, with a default of 500 pesos.
- **3rd offense or higher** — the system flags it as grounds for **Lease Termination**.
- If the violation severity is **"serious"** — such as illegal activity or property destruction — it triggers **immediate lease termination** regardless of the offense count.

When a fine is issued, the system **automatically creates a billing item** and adds it to the tenant's next billing. The violation is assigned a unique **violation number** in the format "VIO-0001".

Each violation goes through a status flow: **Issued**, then **Acknowledged** when the tenant confirms they have seen it, and finally **Resolved**.

### 3.8 Maintenance Request Processing

**[Navigate to Maintenance page]**

The manager also processes **maintenance requests** submitted by tenants. Each request shows the **ticket number**, **category**, **tenant name**, **unit**, and **status**.

The system uses an **Urgency Evaluator service** that automatically assigns an urgency level based on the category and keywords in the description:

- **Level 1 (Critical)** — triggered by keywords like "fire", "flood", "gas leak", "collapse", or "electrocution"
- **Level 2 (High)** — triggered by keywords like "leak", "broken", "clogged", or "pest infestation"
- **Level 3 (Medium)** — the default for most categories
- **Level 4 (Low)** — triggered by keywords like "paint", "scratch", or "cosmetic"

The manager can update the request status from **Pending** to **Ongoing** to **Completed**, and can log **maintenance costs** and completion details.

### 3.9 Announcements

**[Explain the announcement feature]**

The manager can create **announcements** for tenants. Each announcement has a **headline** (3 to 200 characters), **details** (10 to 1,000 characters), a **target property**, and a **notification date** that must be today or in the future. The announcement is sent to all tenants in the manager's assigned units.

**[Log out]**

Now let me switch to the Tenant's perspective.

---

## SECTION 4: TENANT FLOW

**[Log in as tenant@example.com / password]**

I am now logged in as **Tricia Tenant**. The system has redirected me to the **Tenant Dashboard**.

### 4.1 Tenant Dashboard

**[Point to the greeting banner]**

At the top, the greeting banner shows the tenant's name and their **unit information** — bed type, bed number, and unit number.

**[Point to the dashboard cards and indicators]**

The dashboard provides a quick summary of everything the tenant needs to know:

- **Due Date** — shows when the next payment is due and how many days remain
- **Outstanding Balance** — shows any unpaid amounts from previous billing periods
- **Monthly Rate** — shows the monthly rent and whether this is a short-term or long-term lease
- **Lease Status** — shows whether the lease is Active or Expired, and how many days remain

**[Point to the Lease Progress Ring]**

There is a **Lease Progress Ring** — a circular visual indicator showing what percentage of the lease has elapsed. If the lease is within 30 days of expiry, the ring **turns red** to alert the tenant.

**[Point to the Billing Cycle Progress]**

The **Billing Cycle Progress** is a timeline bar showing where the tenant is in the current billing period — how many days until the due date, and what the outstanding balance is.

**[Point to Action Items if visible]**

The dashboard also shows **Action Items** — red flag indicators that appear when the tenant has something that needs attention, such as an overdue payment, an unsigned contract, an open maintenance request, or an expiring lease.

The dashboard has two tabs: **Overview** and **Inspection & Contract** — the second tab allows the tenant to view their move-in inspection checklist, sign contracts, and review their contract terms.

### 4.2 Payment Submission

**[Navigate to Payment page]**

This is the **Payment** page. The tenant can see their **complete billing history** organized into tabs: **All**, **Upcoming** (unpaid), **Paid**, and **Unpaid** (overdue). They can search by reference number, category, or status, and sort by newest or oldest.

**[Click the Pay button or explain the payment flow]**

When the tenant clicks Pay, a **four-step payment modal** opens:

**Step 1**: The tenant selects which **unpaid or overdue billing** they want to pay.

**Step 2**: The tenant selects their **payment method** — GCash, Maya, or Bank Transfer. The system displays **payment instructions** for the selected method.

**Step 3**: This is the **proof of payment form**. The tenant enters their **reference number** (required, maximum 100 characters), the **amount** is pre-filled with the full billing amount — the system enforces **full payment only**, partial payments are not allowed — and they upload a **proof of payment image** such as a screenshot of their transaction (required, maximum 10 megabytes). They also select a **payment category**.

**Step 4**: A **success confirmation** appears, telling the tenant that their payment is now **pending verification** by the manager.

After submission, the payment appears in their history with a **pulsing "Pending Verification" indicator**. Once the manager confirms it, the status changes to **Paid**.

Now, for the **sad path**: if the manager **rejects** the payment, the tenant will see the **rejection reason** displayed on their payment record. They can then **resubmit** — the system allows them to upload a new proof image or correct the reference number and try again. When resubmitting, the previous proof image is retained if they do not upload a new one.

If the tenant tries to submit without entering a reference number, without uploading a proof image, or with a file larger than 10 megabytes — the system will show **validation errors** and prevent submission.

**[Show receipt view if available]**

The tenant can also view a **receipt** for each billing. The receipt includes the **invoice number**, issued date, due date, tenant information, unit details, an **itemized breakdown of charges**, payment information including the method and transaction ID, and the **manager who processed it**.

### 4.3 Maintenance Request

**[Navigate to Maintenance page]**

This is the **Maintenance** page. The tenant can see all their submitted requests organized into tabs: **All**, **Pending**, **Ongoing**, and **Completed**.

**[Click Add Maintenance Request or explain the flow]**

To submit a new request, the tenant clicks the Add button, which opens a **four-step modal**:

**Step 1**: Select the **category** — Plumbing, Electrical, Structural, Appliance, or Pest Control.

**Step 2**: Describe the **problem** — minimum 10 characters, maximum 2,000 characters.

**Step 3**: Optionally upload **up to 3 photos** showing the issue — each photo must be JPEG or PNG format and no larger than 5 megabytes.

**Step 4**: **Review and confirm** the details, then submit.

The system automatically generates a **ticket number** in the format "MR-0001" and uses the **Urgency Evaluator** to assign an urgency level based on the description. For example, if the tenant writes "there is flooding in the bathroom," the keyword "flooding" triggers a **Level 1 Critical urgency**. If they write "the paint is peeling," the keyword "peeling" triggers a **Level 4 Low urgency**.

While the request is still in **Pending** status, the tenant can **edit** it — change the category, update the description, or modify the photos. Once the manager starts working on it and the status changes to **Ongoing**, the edit button **disappears** because the work has already begun.

When the request is marked as **Completed**, the tenant can submit **feedback** — a rating from **1 to 5 stars**, an experience tag, and a comment. Only **one feedback per request** is allowed.

### 4.4 Violation Acknowledgement

**[Navigate to Violations page]**

This page shows all violations issued to the tenant. The list is organized into tabs: **All**, **Issued**, **Acknowledged**, and **Resolved**.

**[Click on a violation or explain the detail view]**

When the tenant clicks on a violation, they see the full details: the **violation number**, **category**, **severity**, **date issued**, **description**, and any **evidence photos** uploaded by the manager. The photos can be clicked to open in a **lightbox** for a closer look.

There is also a **penalty card** showing what penalty was applied — whether it is a Written Warning, a Fine with the amount shown, or a Lease Termination warning.

Below that is the **Offense History Timeline**, which shows all violations issued to this tenant on this lease. This gives context — the tenant can see that this is their first, second, or third offense.

If the violation status is **Issued**, the tenant can click **"Acknowledge Violation"** to confirm that they have seen and understood the notice. This updates the status to **Acknowledged** and records the timestamp. The manager is also notified. If the violation has already been acknowledged, the button is no longer available.

### 4.5 Contract Signing (Tenant Side)

**[Navigate to Inspection & Contract tab on dashboard]**

From the dashboard's **Inspection & Contract** tab, the tenant can view their contract, review the terms, and **sign with an e-signature** when it is their turn. They must first check the **"I agree to the terms and conditions"** checkbox before the signature is accepted. Once all three parties have signed, the contract status becomes **Executed** and a signed copy is stored in the system.

---

## SECTION 5: MOVE-OUT PROCESS

Let me briefly explain the **move-out workflow**, which is the final stage of the tenant lifecycle.

When a tenant decides to move out:

1. The **move-out is initiated** — the system records the date and the tenant provides a **forwarding address** and **reason for vacating**.

2. The manager conducts a **Move-Out Inspection** — they go through the same item checklist from the move-in inspection and record the current condition. For each item, they note whether it was **returned**, its **condition**, the **quantity returned**, and any **repair or replacement costs**. The system then **compares the move-in and move-out inspections** to identify damages.

3. The system **automatically calculates the deposit refund**. The calculation considers:
    - The original **security deposit** amount
    - Any **unpaid billings** — rent, utilities, late fees, violation fines — which are deducted
    - **Advance rent credit** — the advance payment is applied toward any unpaid balance
    - **Damage costs** — determined by comparing move-in versus move-out inspections
    - **Unreturned or partially returned items** — replacement costs are deducted
    - **Deposit interest** — calculated daily at the property's configured annual rate, following **RA 9653 IRR Section 7b**
    - If there is an **early termination** — meaning the tenant leaves before the contract end date — the **entire deposit is forfeited** as per Civil Code Article 1306

    The system only deducts **unpaid** charges to avoid double-counting amounts the tenant has already paid.

4. A separate **Move-Out Contract** is generated and goes through the same **three-party e-signature process** — Manager, Owner, and Tenant must all sign.

5. The **deposit refund is processed** with a recorded reference number, refund method, and completion date.

---

## SECTION 6: SYSTEM ARCHITECTURE AND TECHNOLOGY SUMMARY

To summarize the technology behind this system:

**Frontend**: Laravel Blade templates with **Livewire** for real-time, reactive user interfaces — forms update instantly without full page reloads.

**Backend**: **Laravel** framework with Eloquent ORM, custom middleware for role-based access control, and service classes for business logic like violation escalation and urgency evaluation.

**Machine Learning**: A **Python FastAPI** microservice running a **cluster-based Linear Regression model** built with **Scikit-learn**. It handles price prediction for unit pricing, and separate ML endpoints handle revenue and maintenance forecasting.

**Database**: PostgreSQL with a relational schema covering users, properties, units, beds, leases, billings, transactions, payments, violations, maintenance requests, inspections, and audit logs.

**Security Features**:

- Role-based access control with custom middleware
- Rate-limited login — 5 attempts per minute
- Password hashing using Laravel's bcrypt
- CSRF protection on all forms
- Soft deletes — records are never permanently removed
- Contract audit logging with full change history
- E-signature with IP address and timestamp tracking

**Key Design Decisions**:

- **No public registration** — accounts are created only by administrators
- **Full payment only** — no partial payments, to simplify reconciliation
- **Graceful degradation** — all ML features have fallback mechanisms so the system continues to function even if the Python API is unavailable
- **RA 9653 compliance** — deposit interest calculations follow Philippine law

---

## CLOSING

That concludes the demonstration of Citispace Forerent. The system covers the **complete rental lifecycle** — from property setup and unit creation with ML-powered pricing, through tenant onboarding and contract signing, to monthly billing and payment verification, maintenance tracking, violation management, and finally the move-out process with automated deposit refund calculations.

Every feature includes **validation** to prevent incorrect data entry, **role-based restrictions** to ensure users only access what they are authorized to see, and **audit trails** for accountability and transparency.

Thank you. I am now ready to take your questions.

---

## APPENDIX: QUICK REFERENCE — SAD PATHS AND EDGE CASES

**(Keep this section for Q&A — do not read aloud unless asked)**

| Scenario                                     | What Happens                                                |
| -------------------------------------------- | ----------------------------------------------------------- |
| Wrong password at login                      | Error message shown, blocked after 5 attempts per minute    |
| Tenant tries to access /landlord URL         | 403 Forbidden — middleware blocks it                        |
| Login without accepting Terms                | Validation error — "terms must be accepted"                 |
| Add property without photo                   | Validation error — at least 1 photo required                |
| Upload file larger than 10MB                 | Validation error — file rejected                            |
| ML Price API is down                         | Falls back to default price range, system does not crash    |
| ML Forecast API is down                      | Falls back to historical averages, flagged as fallback data |
| Partial payment attempt                      | Not allowed — amount is enforced to full billing amount     |
| Payment rejected by manager                  | Tenant sees rejection reason, can resubmit                  |
| Submit maintenance with <10 char description | Validation error                                            |
| Upload >3 photos on maintenance              | Validation error                                            |
| Edit maintenance after status is Ongoing     | Edit button disappears — cannot edit once work has started  |
| Submit feedback twice on same request        | Only one feedback allowed per request                       |
| Violation date set in the future             | Validation error — date must be today or earlier            |
| Evidence photo >5MB                          | Validation error — file rejected                            |
| Serious violation on first offense           | Immediate lease termination regardless of offense count     |
| Acknowledge already-acknowledged violation   | Button is no longer visible                                 |
| Sign contract without checking "I agree"     | Blocked — checkbox required                                 |
| Assign >10 units to one manager              | Blocked — maximum 10 units enforced                         |
| Utility bill for unit with no tenants        | Cannot submit — no active leases                            |
| Early termination move-out                   | Full deposit forfeited — refund is zero                     |
| Concurrent ticket number generation          | Advisory database lock prevents duplicates                  |

---

## APPENDIX: DEMO ACCOUNTS

| Role      | Email                | Password |
| --------- | -------------------- | -------- |
| Landlord  | landlord@example.com | password |
| Manager   | manager@example.com  | password |
| Tenant    | tenant@example.com   | password |
| Manager 2 | manager2@example.com | password |
| Tenant 2  | tenant2@example.com  | password |
