# CITISPACE FORERENT - Thesis Defense Demonstration Script

> **Instructions**: Read this script while demonstrating the system. Text in **[brackets]** are actions you perform on screen. Text in **(parentheses)** are notes to yourself — do not read aloud.

---

## OPENING

Good day, everyone. Today I will be demonstrating **Citispace Forerent**, a web-based rental property management system designed for dormitory-style properties. The system serves three types of users — the **Property Owner or Landlord**, the **Property Manager**, and the **Tenant** — each with their own dashboard and set of features tailored to their role.

The system is built using **Laravel** with **Livewire** for real-time, reactive user interfaces, and it integrates a **Python-based machine learning API** using FastAPI and Scikit-learn for intelligent pricing and forecasting. It also follows Philippine landlord-tenant law, specifically **Republic Act 9653**, for deposit interest calculations and tenant protections, **Republic Act 8792** for electronic signature compliance, and **Republic Act 10173** for data privacy.

Let me walk you through the entire system, starting from how users access it.

---

## SECTION 1: AUTHENTICATION AND ROLE-BASED ACCESS

**[Open the login page in the browser]**

This is the login page of Citispace Forerent. The system does not have a public registration feature — this is intentional. Only authorized administrators can create user accounts, which prevents unauthorized access to the platform. Users log in using their **email and password**.

The system also requires the user to **accept the Terms and Conditions** before logging in. If a user — particularly a tenant — has not yet accepted the terms, they are redirected to the **Terms of Service page** where they must review and accept before they can proceed. The system records the **exact timestamp** of when terms were accepted.

**[Show the Terms of Service page briefly]**

The system also provides a **Privacy Policy** page and a **Data Protection** page, both accessible from the login screen. The Data Protection page documents our compliance with **Republic Act 10173**, the Data Privacy Act, including our encryption standards, data retention policies, and breach notification procedures.

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

Next, we have the **Contracts Awaiting Signature** section. This shows up to five pending contracts that need signatures. Each contract shows three indicators — **O, M, and T** — which stand for **Owner, Manager, and Tenant**. The indicator lights up green when that party has already signed, and a counter shows how many out of three signatures are complete. If the owner still needs to sign, a **"Sign Now"** button appears. There is also a link to view all contracts.

**[Point to the Financial Overview section]**

Below that is the **Financial Overview**. On the left, we have a **Revenue vs. Expenses chart** — this is a 12-month line graph showing monthly **income from rent** compared to **expenses from maintenance**, with interactive hover points and gradient fills. On the right is a **Rent Collection summary** that shows the **total collected rent versus uncollected rent** with percentages, filterable by month. The owner can see their collection rate at a glance.

**[Point to the Announcements and Calendar sections]**

The owner also has access to an **Announcements** section showing announcements sorted by upcoming dates, and a **Calendar Widget** for tracking events. The calendar highlights announcement dates with visual indicators, and clicking a date shows that day's announcements.

### 2.2 Property Management

**[Navigate to the Property page]**

This is the **Property Management** page. It has **two tabs**: **Properties** and **Contracts**.

#### Properties Tab

**[Point to the building cards]**

The Properties tab lists all properties owned by this landlord as **building cards**, showing the building name, address, unit count, and property image.

**[Click on a property or explain the details panel]**

When a property is selected, the right panel shows the **Property Details** — the building name, address, description, a **photo carousel** for scrolling through property images, and a list of uploaded **legal documents** such as the Business Permit, BIR 2303, Barangay Clearance, and Occupancy Permit.

**[Point to the Bed Status chart]**

There is also a **Bed Status Widget** — an interactive donut chart showing the ratio of **occupied beds** versus **available beds**, with hover tooltips displaying exact counts and percentages.

**[Point to the units accordion]**

Below that is the **Units Accordion** — an expandable list showing each unit with its unit number, room type, capacity, occupancy status, and pricing.

**[Click Add Property — or explain the form]**

When adding a property, the owner fills in the **building name**, **address**, and **description**. They are required to upload at least **one property photo** — each photo has a maximum file size of **10 megabytes**. They also upload important **legal documents**. These documents are required when creating a property for the first time.

If the owner tries to submit without filling in required fields — for example, leaving the building name blank or not uploading a photo — the system will show a **validation error** and prevent submission.

#### Contracts Tab

**[Switch to the Contracts tab]**

The Contracts tab shows all lease contracts across all properties. It has **three sub-tabs**: **All**, **Pending**, and **Signed**. The owner can search by **tenant name, property, or unit**, and filter by **month, year, or building**.

Each contract displays the tenant name, property, unit, contract status, and signature count. Clicking a contract opens the **Landlord Contract Viewer Modal**, which shows the full contract document with all three signature blocks and their statuses. The owner can **sign directly from this modal** when it is their turn.

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

The landlord can also manage **Property Managers** from this page. On the left is a **Manager List** showing all managers assigned to the landlord's properties. Selecting a manager displays their **detail panel** on the right, showing their personal information, the buildings they manage, the units under their care, and total counts.

**[Click Add Manager or explain the form]**

When adding a manager, the landlord fills in the manager's personal details — name, email, and contact number — and then assigns them to specific **units**. The assignment is done by selecting a building, then a floor, and then choosing which units on that floor the manager will handle.

There is a **maximum of 10 units per manager** to ensure manageable workloads. Only unassigned units are shown in the selection. Once saved, the manager receives a **welcome email via SendGrid** with their login credentials, including a system-generated secure password.

### 2.5 Payment Monitoring

**[Navigate to Payment page]**

This is the **Payment Monitoring** page, where the landlord can oversee all rent payments across all properties. It has **four tabs**: **All**, **Paid**, **Unpaid**, and **Overdue**.

The landlord can filter by **building**, **month**, and **year**, and search by **billing ID, tenant name, or unit**. Each entry shows the billing date, tenant, property, unit, amount, due date, and status.

**[Click on a payment or explain the receipt modal]**

Clicking on a payment opens the **Receipt Viewer Modal**, which shows the complete billing details — the billing date, due date, amount due, tenant and property details, lease term information, manager contact info, previous balance tracking, an itemized breakdown of charges, and the transaction record if paid.

### 2.6 Revenue Reports and Forecasting

**[Navigate to Revenue page]**

This section provides **analytics, reporting, and forecasting** tools, organized into sub-sections.

**[Point to the Inflow/Outflow chart]**

The **Inflow and Outflow chart** shows a 12-month comparison of income from rent payments versus expenses from maintenance, with year selection.

**[Point to the Maintenance Cost Breakdown]**

The **Maintenance Cost Breakdown** is a pie chart showing how maintenance costs are distributed across categories — Plumbing, Electrical, Structural, Appliance, and Pest Control. This can be toggled between a **monthly** or **full year** view.

**[Point to the Revenue Records section]**

There is also a **Revenue Records** section — a searchable, filterable transaction log showing the date, type, category, amount, and running balance for every financial transaction in the system.

**[Point to the Revenue Forecast]**

The **Revenue Forecasting** section uses a separate **Python machine learning API** that analyzes historical rent payment data and predicts future revenue. It shows a **year selector**, **monthly forecasts** with actual versus predicted comparison, **total annual revenue projection**, **total remaining revenue** for future months, **average monthly revenue**, and the **number of data points** used for the prediction.

**[Point to the Maintenance Forecast]**

Similarly, the **Maintenance Forecasting** section predicts future maintenance costs. It displays **maintenance statistics** — total historical records, date range, total historical cost, and average monthly cost — along with **monthly predictions** and **building-wise maintenance cost breakdowns** with trend comparisons.

If either forecasting API is unavailable, the system falls back to **historical averages** and flags the result as fallback data so the owner knows it is estimated. The APIs use **retry logic with exponential backoff** — up to 3 attempts with a 120-second timeout — to maximize reliability.

### 2.7 Tenant Overview

**[Navigate to the Tenant section if accessible from landlord view]**

The landlord can also view tenant information. The system shows a **Lease Expiration Overview** widget with counts of leases **expiring this month**, **within 30 days**, and **within 60 days**, along with the **average turnaround time** metric.

The tenant list shows tenant details including their personal information, lease terms, unit assignments, payment status, and violation history. The landlord can also view and sign both **move-in contracts** and **move-out contracts** from this section.

### 2.8 Maintenance Tracking

**[Navigate to the Maintenance section]**

The landlord has visibility into **all maintenance requests** across all properties. The list can be filtered by status — **All**, **Open**, **Pending**, **Completed**, or **Cancelled** — and shows the ticket number, urgency level, category, tenant, unit, and created date.

Selecting a request shows the full details, timeline of updates, cost breakdown, and the assigned manager. The **Maintenance Status Metrics** widget shows the **total maintenance cost year-to-date**, **new requests this month**, and **pending requests count**.

### 2.9 Messaging

**[Navigate to the Messages page]**

The landlord has access to a **Messaging System** for communicating with managers and tenants. This includes a full **conversation thread interface** and a **floating chat widget** for quick access.

### 2.10 Settings

**[Navigate to the Settings page]**

The Settings page allows the landlord to update their **personal information** — name, email, phone, government ID — and manage **security settings** including password changes. The password must meet strength requirements: minimum 8 characters, with uppercase, lowercase, numbers, and special characters. The system **automatically logs out** the user after a password change for security.

**[Log out]**

That covers the Landlord's capabilities. Now let me switch to the Manager role.

---

## SECTION 3: MANAGER FLOW

**[Log in as manager@example.com / password]**

I am now logged in as **Marcus Manager**. The system has redirected me to the **Manager Dashboard**.

### 3.1 Manager Dashboard

**[Point to the dashboard elements]**

The Manager's dashboard shows a **greeting banner**, an **announcements section** for messages relevant to the manager's properties, a **calendar widget** with highlighted dates, and **Maintenance Statistics** — showing the total maintenance cost, new requests this month, and pending request counts. This gives the manager a quick overview of their workload.

**[Point to the Announcement Modal if applicable]**

The manager can also **create announcements** from the dashboard. Each announcement has a **headline** (3 to 200 characters), **details** (10 to 1,000 characters), a **target property** selected from the manager's assigned properties, and a **notification date** that must be today or in the future. The announcement is sent to all tenants in the manager's assigned units, and a **notification** is pushed to their notification bell.

### 3.2 Property View

**[Navigate to the Property page]**

The manager can view the **properties and units** they are assigned to. Unlike the landlord, the manager sees only the buildings and units where they have been assigned. They can view property details, unit information, bed status, and occupancy — but they **cannot add new properties or units**. That is the landlord's responsibility.

### 3.3 Tenant Management

**[Navigate to Tenant Management page]**

This is the **Tenant Management** page. It shows a list of tenants organized into **four tabs**:

1. **Current** — tenants with active leases, no move-out initiated
2. **Moving Out** — tenants with active leases who have initiated the move-out process
3. **Transferred** — tenants whose previous lease expired but moved to a different unit
4. **Moved Out** — tenants who have completely vacated

Each tab shows its count. The manager can **search by name, unit number, or bed number**, **filter by building**, and **sort by newest or oldest**.

**[Click on a tenant or explain the Add Tenant flow]**

When adding a new tenant, the manager fills in their **personal information** — first name, last name, email, contact number, gender, permanent address — along with their **government ID details** and **emergency contact information**. The manager then assigns the tenant to a specific **bed in a managed unit** and sets the **lease terms**: start date, end date, lease term in months, shift — Morning or Night — the contract rate, security deposit, advance payment, and the monthly due date.

Once saved, the system creates a **lease record** in draft status, generates the tenant's login credentials, and sends a **welcome email via SendGrid** with their temporary password.

### 3.4 Contract and E-Signature Workflow

**[Navigate to or explain the Contract panel]**

The contract workflow is a **three-party e-signature process** compliant with **Republic Act 8792**, the Electronic Commerce Act. When a lease is created, the contract starts in **Draft** status. The signing follows a strict order:

1. The **Owner signs first** — the status becomes "Pending Manager"
2. The **Manager signs as witness** — the status becomes "Pending Tenant"
3. The **Tenant signs last** — the status becomes "Executed"

Each signature records four pieces of information: the **signature image** (stored as PNG), the **timestamp** of when it was signed, the **IP address** of the signer, and a **SHA-256 content hash** for tamper-proof integrity. This creates a complete **audit trail** for legal accountability under RA 8792.

There is also a **Contract Audit Log** that records every change made to any contract — who changed it, what was changed, the old value, the new value, metadata including IP and user agent, and when. This provides full transparency and accountability.

If any party tries to sign without first accepting the terms and conditions, the system will **block the signature** and show an error. Each party must confirm: *"This electronic signature is legally binding under RA 8792."*

### 3.5 Move-In Inspection

**[Explain the inspection form]**

Before a tenant fully moves in, the manager conducts a **Move-In Inspection**. This uses a **centralized checklist configuration** with **9 inspection items**:

- Bed Frame & Mattress
- Cabinet/Wardrobe
- AC Unit & Remote
- Bathroom Fixtures
- Electrical Outlets
- Windows/Curtains
- Walls condition
- Floor condition
- Door locks

For each item, the manager records the **condition** (good, damaged, or missing), **quantity**, and any **remarks**.

The manager also records **5 items received** by the tenant:
- Unit keys
- Building access card/fob
- Wi-Fi credentials
- AC remote
- Cabinet key

The tenant then **confirms receipt** of these items. This is important because at move-out, the system will **compare the move-in and move-out inspections** to determine damages or missing items.

If the tenant disagrees with any item's recorded condition, they can raise a **dispute**, and the manager can provide a resolution.

### 3.6 Billing Management

**[Navigate to Payment/Billing page]**

The Payment Documents page has **three tabs**: **Rent Payments**, **Payment Requests**, and **Utility Bills**.

#### Rent Payments Tab

This shows all billing records with **four sub-tabs**: All, Paid, Unpaid, and Overdue. The manager can **filter by building, month, and year**, and **search by billing ID, tenant name, or unit**. Each billing contains **itemized charges**:

- **Rent** — the recurring monthly rate from the contract
- **Electricity Share** — the tenant's share of the unit's electricity bill
- **Water Share** — the tenant's share of the unit's water bill
- **Short-term Premium** — an additional charge if the lease term is less than 6 months
- **Late Payment Fee** — a percentage-based penalty calculated daily for overdue payments, **capped at 25% of monthly rent** per Civil Code Article 1229
- **Violation Fees** — any fines from violations

Each billing has a **status**: Unpaid, Overdue, or Paid. The system also tracks **previous balances** — if a tenant did not pay last month, that unpaid amount carries over to the next billing period.

#### Payment Requests Tab

**[Switch to the Payment Requests tab]**

When a tenant submits a payment, it appears here as a **Payment Request** with a status of **Pending**. This tab has **four sub-tabs**: All, Pending, Confirmed, and Rejected. The manager can filter by building, month, year, and search by tenant name, reference number, or payment method.

**[Click on a payment request]**

The manager can see the payment details — the amount, payment method, reference number, payment category, and the **proof of payment image** the tenant uploaded.

The manager has two options:

1. **Confirm** the payment — this updates the payment request to "Confirmed", creates a **Transaction record** with the appropriate prefix (RENT, ADV, or DEP depending on billing type), updates the billing status to "Paid", and sends a **notification to the tenant** confirming receipt.

2. **Reject** the payment — the manager selects from **predefined rejection reasons**: Insufficient Funds, Wrong Amount, Invalid Reference, Duplicate Payment, or Other. If "Other" is selected, the manager must provide a custom description (5 to 500 characters). At least one reason is required. The tenant receives a notification explaining why their payment was rejected, and they can **resubmit** with corrections.

#### Utility Bills Tab

**[Switch to the Utility Bills tab]**

This is where the manager enters **utility bills** for each unit. The manager selects the **building**, **unit**, **utility type** (Electricity or Water), **billing period** (month and year), and the **total amount**.

The system then **automatically calculates each tenant's share**. If a tenant moved in mid-month, their share is **prorated** based on the number of days they occupied the unit. The system shows a complete breakdown of how the bill is split among all occupants, with a **proration flag** indicator for any prorated amounts.

The Utility Bill Table shows all entered bills with tabs for **All**, **Electricity**, and **Water**, filterable by building, month, and year.

### 3.7 Violation Management

**[Navigate to Violations page]**

The manager can issue **violations** against tenants. The violation list has **four tabs**: All, Issued, Acknowledged, and Resolved. The manager can search by **violation number, category, unit, or tenant name**, filter by **building**, and sort by **newest, oldest, or most severe**.

**[Click Add Violation or explain the modal]**

When creating a violation, the manager selects the **tenant** from a dropdown of active tenants, chooses a **category** — such as Noise Violation or Property Damage — selects the **severity level** (minor, major, or serious), writes a **description**, sets the **violation date** (which cannot be in the future), and can upload up to **3 evidence photos** (maximum 5 megabytes each).

The modal includes a **Penalty Preview** that updates in real-time as the manager changes the severity and tenant selection. It uses the **Violation Escalation Service** to determine the penalty based on the tenant's **offense history**:

- **1st offense** — the tenant receives a **Written Warning**. No fine is charged.
- **2nd offense** — the tenant is charged a **Fine**. The fine amount is configured in the property's contract settings, with a default of 500 pesos.
- **3rd offense or higher** — the system flags it as grounds for **Lease Termination**.
- If the violation severity is **"serious"** — such as illegal activity or property destruction — it triggers **immediate lease termination** regardless of the offense count.

When a fine is issued, the system **automatically creates a billing item** and adds it to the tenant's next billing. The violation is assigned a unique **violation number** in the format "VIO-0001".

**[Click on a violation to show the detail view]**

The violation detail shows all information plus the **offense history** — a timeline of all violations for this tenant's lease, showing the escalation progression. The manager can **resolve** a violation by adding **resolution notes** (3 to 1,000 characters), which updates the status to "Resolved" and sends a notification to the tenant.

### 3.8 Maintenance Request Processing

**[Navigate to Maintenance page]**

The maintenance page uses a **two-panel layout** — the list on the left and the detail on the right. Each request shows the **ticket number** (MR-XXXX format), **status badge**, **category**, **unit**, **tenant name**, and **created date**. The list can be filtered by **building**, searched, and sorted.

The system uses an **Urgency Evaluator service** that automatically assigns an urgency level based on the category and keywords in the description:

- **Level 1 (Critical)** — triggered by keywords like "fire", "flood", "gas leak", "collapse", or "electrocution"
- **Level 2 (High)** — triggered by keywords like "leak", "broken", "clogged", or "pest infestation"
- **Level 3 (Medium)** — the default for most categories
- **Level 4 (Low)** — triggered by keywords like "paint", "scratch", or "cosmetic"

**[Click on a maintenance request to show the detail panel]**

The detail panel shows the full request information plus several **manager action areas**:

1. **Maintenance Cost Tracking** — the manager can add cost items with amount and description, select whether the cost is charged to the **Owner** or the **Unit** (meaning the tenant). A **visual warning** appears when costs exceed ₱10,000. Costs charged to the unit create corresponding billing items for the tenant.

2. **Manager Notes** — a timestamped note system where the manager can log updates, observations, or instructions for each request.

3. **Activity Log** — an automatic audit trail of all status changes, providing a complete timeline of the request's lifecycle.

4. **Request Tracking** — fields for the assigned person, expected completion date, and urgency level.

The manager can update the request status from **Pending** to **Ongoing** to **Completed**, and the activity log records each transition.

### 3.9 Projected Maintenance Costs

**[Point to the Projected Maintenance Cost widget if visible]**

The manager also has a **Projected Maintenance Cost** widget that shows current month costs by building, trend indicators (up, down, or stable compared to the previous month), and a year-to-date monthly chart. This helps with budget planning.

### 3.10 Messaging

**[Navigate to the Messages page]**

The manager has access to the **Messaging System** for communicating with tenants and the landlord. The interface supports **conversation threads**, **message history**, **unread counts**, and **file attachments** including images and documents. There is also a **media gallery** in each conversation that organizes shared files into Images and Documents tabs.

**[Log out]**

Now let me switch to the Tenant's perspective.

---

## SECTION 4: TENANT FLOW

**[Log in as tenant@example.com / password]**

I am now logged in as **Tricia Tenant**. The system has redirected me to the **Tenant Dashboard**.

### 4.1 Tenant Dashboard — Overview Tab

**[Point to the greeting banner]**

At the top, the greeting banner shows the tenant's name and their **unit information** — bed type, bed number, and unit number.

**[Point to the Payment Banner]**

Below the greeting is a **Payment Banner** showing the current billing amount due, the due date with a countdown, payment status, and outstanding balance. If the payment is overdue, this banner **turns red** with a "Pay Now" button.

**[Point to the dashboard cards]**

The dashboard provides **four key metric cards**:

- **Due Date** — shows when the next payment is due and how many days remain
- **Outstanding Balance** — shows any unpaid amounts from previous billing periods
- **Monthly Rate** — shows the monthly rent and whether this is a short-term or long-term lease
- **Lease Status** — shows whether the lease is Active or Expired, and how many days remain

**[Point to the Lease Progress Ring]**

There is a **Lease Progress Ring** — a circular visual indicator showing what percentage of the lease has elapsed, along with lease term details including months, shift, and auto-renewal status. If the lease is within 30 days of expiry, the ring **turns red** to alert the tenant.

**[Point to the Billing Cycle Progress]**

The **Billing Cycle Progress** is a timeline bar showing where the tenant is in the current billing period — the due date, overdue status, and next billing date.

**[Point to the Utilities Summary]**

The **Utilities Summary Widget** shows the current month's **electricity share** and **water share** amounts, with a total and per-utility breakdown bars. This links to the full Utility History page.

**[Point to the Contract Status Widget]**

The **Contract Status Widget** shows a **3-step signing pipeline visualization** — Owner, Manager/Witness, and You (Tenant). Each step shows whether that party has signed or is waiting. If it is the tenant's turn, a **"Read & Sign Contract"** button appears.

**[Point to the Maintenance Widget]**

The **Maintenance Request Widget** shows the count of open maintenance requests and a list of the most recent requests with status badges, linking to the full Maintenance page.

**[Point to the Move Dates Widget]**

The **Move Dates Widget** displays the move-in date and move-out date (if scheduled).

**[Point to the Violation Records section if violations exist]**

If the tenant has any violations, a **Violation Records** section appears showing the total count with a breakdown by status (Issued, Acknowledged, Resolved). Each violation card shows the violation number, category, severity, offense number, penalty type and amount, and an **"Acknowledge"** button for unacknowledged violations. There is also a **Penalty Schedule Reference** showing what happens at each offense level.

**[Point to the Payment Requests section if visible]**

The dashboard also shows **Pending Payment Requests** — payments submitted but awaiting manager verification — and **Rejected Payment Requests** with the rejection reason and a **"Re-submit Payment"** button.

### 4.2 Tenant Dashboard — Inspection & Contract Tab

**[Switch to the Inspection & Contract tab]**

This tab has two sections: **Move-In** and **Move-Out** (if applicable).

The **Move-In section** shows the **items received checklist** with photo previews, a confirmation checkbox, and the full **lease contract document**. The tenant can review all contract terms — lessor information, personal details, rent details, move-in details, and all three signatures with timestamps.

If a move-out has been initiated, the **Move-Out section** appears with the **items returned checklist**, the **move-out contract**, deposit refund information, and the option to **dispute inspection items** by adding remarks and evidence.

The tenant can also **download signed contracts as PDF** from this tab.

### 4.3 Payment Submission

**[Navigate to Payment page]**

The Payment page has **two tabs**: **Rent Payments** and **Utility Bills**.

#### Rent Payments Tab

**[Point to the payment banner and billing list]**

At the top is a **payment banner** showing the current amount due, due date countdown, status, and counts of pending and rejected payment requests.

The billing history is organized into tabs: **All**, **Upcoming** (unpaid), **Paid**, and **Unpaid** (overdue). The tenant can search and sort by newest or oldest.

**[Click the Pay button or explain the payment flow]**

When the tenant clicks Pay, a **four-step payment modal** opens:

**Step 1**: The tenant selects which **unpaid or overdue billing** they want to pay.

**Step 2**: The tenant selects their **payment method** — GCash, Maya, or Bank Transfer. The system displays **payment instructions** for the selected method.

**Step 3**: This is the **proof of payment form**. The tenant enters their **reference number** (required, maximum 100 characters), the **amount** is pre-filled with the full billing amount — the system enforces **full payment only**, partial payments are not allowed — they select a **payment category**, and they upload a **proof of payment image** such as a screenshot of their transaction (required, maximum 5 megabytes).

**Step 4**: A **success confirmation** appears, telling the tenant that their payment is now **pending verification** by the manager.

After submission, the payment appears in their history with a **"Pending Verification" indicator**. Once the manager confirms it, the status changes to **Paid**.

For the **sad path**: if the manager **rejects** the payment, the tenant sees the **rejection reason** displayed on their payment record. They can then **resubmit** — the system allows them to upload a new proof image or correct the reference number and try again.

**[Show receipt view if available]**

The tenant can view a **Receipt** for each billing by clicking the view button. The receipt includes the **invoice number**, issued date, due date, tenant information, unit details, an **itemized breakdown of charges**, payment information including the method and transaction ID, and the **manager who processed it**. The receipt can be **downloaded as a PDF**.

#### Utility Bills Tab

**[Switch to the Utility Bills tab]**

This is the **Utility History** page. The tenant can see all their utility bills organized into tabs: **All**, **Electricity**, and **Water**. Each entry shows the billing period, utility type, total bill amount, the tenant's share, and the tenant count in the unit. The tenant can filter by **month** and **year**, and rows are expandable to show detailed billing period information.

### 4.4 Maintenance Request

**[Navigate to Maintenance page]**

The maintenance page uses a **two-panel layout** — list on the left and detail on the right. The list has tabs: **All**, **Pending**, **Ongoing**, and **Completed**, each showing their count.

**[Click Add Maintenance Request or explain the flow]**

To submit a new request, the tenant clicks the Add button, which opens a **four-step modal**:

**Step 1**: Select the **category** — Plumbing, Electrical, Structural, Appliance, or Pest Control.

**Step 2**: Describe the **problem** — minimum 10 characters, maximum 2,000 characters, with a real-time character count.

**Step 3**: Optionally upload **up to 3 photos** showing the issue — each photo must be JPEG or PNG format and no larger than 5 megabytes. Photos can be previewed and removed.

**Step 4**: **Review and confirm** the details, then a **confirmation modal** appears for final verification before submission.

The system automatically generates a **ticket number** in the format "MR-0001" and uses the **Urgency Evaluator** to assign an urgency level based on the description. For example, if the tenant writes "there is flooding in the bathroom," the keyword "flooding" triggers a **Level 1 Critical urgency**.

**[Click on a maintenance request to show the detail panel]**

The detail panel shows the ticket number, status, urgency, category, description, photos, and the **activity timeline** showing all status updates and manager notes.

While the request is still in **Pending** status, the tenant can **edit** it — change the category, update the description, or modify the photos. Once the status changes to **Ongoing**, the edit button **disappears** because the work has already begun.

When the request is marked as **Completed**, the tenant can submit **feedback** — a rating from **1 to 5 stars**, an experience tag, and a comment. Only **one feedback per request** is allowed, and the system enforces this.

If costs have been logged by the manager, the tenant can see the **cost breakdown** as a read-only view.

### 4.5 Violation Acknowledgement

**[Navigate to Violations — accessible from dashboard or as a section]**

The violation list has tabs: **All**, **Issued**, **Acknowledged**, and **Resolved**, each with counts. The tenant can search by violation number, category, or status, and sort by newest or oldest.

**[Click on a violation or explain the detail view]**

When the tenant clicks on a violation, they see the full details: the **violation number**, **category**, **severity**, **date issued**, **description**, and any **evidence photos** uploaded by the manager.

There is a **penalty card** showing what penalty was applied — whether it is a Written Warning, a Fine with the amount shown, or a Lease Termination warning.

Below that is the **Offense History Timeline**, which shows all violations issued to this tenant on this lease, showing the escalation progression.

If the violation status is **Issued**, the tenant can click **"Acknowledge Violation"** — a confirmation modal appears, and upon confirmation, the status updates to **Acknowledged** with a timestamp. The manager is notified. If the violation has already been acknowledged, the button is no longer available.

If the violation has been **Resolved**, the tenant can see the **resolution notes** and resolution date provided by the manager.

### 4.6 Contract Signing (Tenant Side)

**[Navigate to Inspection & Contract tab on dashboard]**

From the dashboard's **Inspection & Contract** tab, the tenant can view their contract, review all terms, and **sign with an e-signature** when it is their turn. They must first check the **"I agree to the terms and conditions"** checkbox and confirm that the electronic signature is legally binding under RA 8792. Once all three parties have signed, the contract status becomes **Executed** and a signed copy is stored in the system.

### 4.7 Messaging

**[Navigate to Messages page]**

The tenant has access to a **Messaging System** for communicating with their assigned manager. The tenant can only message managers — this is a role restriction for security.

If no prior conversation exists, the system shows **concern topic suggestions** — such as Maintenance Issues, Billing Questions, or General Inquiries — to help start the conversation. The chat supports **text messages**, **file attachments** (images and documents), **delivery status** tracking, **unread counts**, and a **media gallery** for shared files.

### 4.8 Settings

**[Navigate to Settings page]**

The tenant's Settings page has **three tabs**:

1. **Personal Information** — edit name, email, phone, government ID, permanent address, emergency contact, company/school information, and profile picture.

2. **Security** — change password with strength validation (8+ characters, uppercase, lowercase, number, special character). The system automatically logs out the user after a password change.

3. **My Unit** — this is a **tenant-exclusive tab** that shows the property information (building name, address, description, photos), the unit details (unit number, floor, occupants, living area, furnishing type, bed type, capacity, monthly price), a list of **amenities**, and access to **property documents** such as permits and clearances organized by category.

### 4.9 Notifications

**[Point to the notification bell in the navigation bar]**

Throughout the system, the tenant receives **real-time notifications** via the **notification bell icon** in the top navigation. The bell shows an **unread count badge** and a dropdown with the last **20 notifications**. Notification types include:

- Payment request confirmations or rejections
- Violation issuance and resolution
- Maintenance request status updates
- Contract status changes
- Payment reminders
- General announcements

The tenant can **mark individual notifications as read** or **mark all as read**, and clicking a notification navigates to the relevant page.

---

## SECTION 5: MOVE-OUT PROCESS

Let me briefly explain the **move-out workflow**, which is the final stage of the tenant lifecycle.

When a tenant decides to move out:

1. The **move-out is initiated** — the system records the date and the tenant provides a **forwarding address**, **reason for vacating**, **deposit refund method**, and **deposit refund account** details.

2. The manager conducts a **Move-Out Inspection** — they go through the same item checklist from the move-in inspection and record the current condition. For each item, they note whether it was **returned**, its **condition**, the **quantity returned**, and any **repair or replacement costs**. The system then **compares the move-in and move-out inspections** to identify damages.

   The manager also records **5 items returned** by the tenant — unit keys, building access card, AC remote, cabinet key, and Wi-Fi credential rotation. The manager can **confirm repair costs** via a toggle, and the tenant can **dispute** individual inspection items with remarks and evidence.

3. The system **automatically calculates the deposit refund**. The calculation considers:
    - The original **security deposit** amount
    - Any **unpaid billings** — rent, utilities, late fees, violation fines — which are deducted (only unpaid charges, to avoid double-counting)
    - **Advance rent credit** — the advance payment is applied toward any unpaid balance
    - **Damage costs** — determined by comparing move-in versus move-out inspections
    - **Unreturned or partially returned items** — replacement costs are deducted
    - **Deposit interest** — calculated daily at the property's configured annual savings rate, following **RA 9653 IRR Section 7b**
    - If there is an **early termination** — meaning the tenant leaves before the contract end date — the **entire deposit is forfeited** as per **Civil Code Article 1306**

4. A separate **Move-Out Contract** is generated and goes through the same **three-party e-signature process** — Owner, Manager, and Tenant must all sign.

5. The **deposit refund is processed** with a recorded reference number, refund method, completion date, and the interest earned is tracked separately.

6. The tenant's status changes to **Moved Out** in the tenant management list.

---

## SECTION 6: AUTOMATED SYSTEM PROCESSES

The system includes several **scheduled automated tasks** that run daily:

1. **Late Fee Application** (`billings:apply-late-fees`) — automatically calculates and applies late payment penalties to overdue rent, capped at 25% of monthly rent per Civil Code Article 1229.

2. **Lease Expiration Handling** (`leases:handle-expiration`) — processes expired leases, handles auto-renewal for eligible tenants, and manages lease terminations.

3. **Non-Payment Checking** (`leases:check-nonpayment`) — checks for tenants with outstanding payments, sends reminders, and escalates as needed.

4. **Deposit Refund Reminders** (`deposits:send-refund-reminders`) — sends automated reminders to owners and managers at **7 days**, **3 days**, and on the **deadline date** for pending deposit refunds, ensuring RA 9653 compliance for timely returns.

These commands run automatically at midnight via Laravel's task scheduler.

---

## SECTION 7: SYSTEM ARCHITECTURE AND TECHNOLOGY SUMMARY

To summarize the technology behind this system:

**Frontend**: Laravel Blade templates with **Livewire** for real-time, reactive user interfaces — forms validate and update instantly without full page reloads. The system contains **75 Livewire components** and **191 Blade template files**.

**Backend**: **Laravel** framework with Eloquent ORM, custom middleware for role-based access control, and **6 service classes** for business logic:
- **ViolationEscalationService** — determines penalties based on offense history
- **UrgencyEvaluator** — classifies maintenance request urgency from keywords
- **RevenueForecastService** — predicts future revenue using ML
- **MaintenanceForecast** — predicts future maintenance costs using ML
- **FirebaseStorageService** — manages file uploads with cloud/local fallback
- **PasswordGenerator** — creates secure random passwords for new accounts

**Machine Learning**: A **Python FastAPI** microservice running a **cluster-based Linear Regression model** built with **Scikit-learn**. It handles price prediction for unit pricing, and separate ML endpoints handle revenue and maintenance forecasting with retry logic and exponential backoff.

**Database**: PostgreSQL with **25 data models** covering users, properties, units, beds, leases, billings, billing items, transactions, receipts, payment requests, payment categories, violations, maintenance requests, maintenance logs, maintenance notes, maintenance activities, maintenance feedback, move-in inspections, move-out inspections, announcements, messages, notifications, contract audit logs, utility bills, and property documents.

**File Storage**: **Firebase Storage** (Google Cloud) as primary with automatic **local disk fallback**. Images are auto-resized to maximum 1600px width with quality optimization.

**Email**: **SendGrid** integration for transactional emails — welcome emails with login credentials, announcements, and notifications.

**PDF Generation**: Server-side PDF rendering for move-in contracts, move-out contracts, and payment receipts.

**Security Features**:

- Role-based access control with custom middleware
- Rate-limited login — 5 attempts per minute
- Password hashing using Laravel's bcrypt
- Password strength requirements (8+ chars, mixed case, numbers, special characters)
- CSRF protection on all forms
- Soft deletes — records are never permanently removed
- Contract audit logging with SHA-256 content hashing
- E-signature with IP address, user agent, and timestamp tracking
- Terms acceptance tracking with timestamps
- Auto-logout after password changes
- Data encryption at rest (AES-256) and in transit (TLS/SSL)

**Legal Compliance**:

- **RA 9653** (Rent Control Act) — deposit limits, interest calculations, refund deadlines
- **RA 8792** (E-Commerce Act) — electronic signature validity with audit trails
- **RA 10173** (Data Privacy Act) — data protection, retention policies, breach notification
- **Civil Code Art. 1306** — early termination deposit forfeiture
- **Civil Code Art. 1229** — late payment fee cap at 25% of monthly rent

**Key Design Decisions**:

- **No public registration** — accounts are created only by administrators
- **Full payment only** — no partial payments, to simplify reconciliation
- **Graceful degradation** — all ML features have fallback mechanisms with retry logic
- **Three-party e-signature** — Owner, Manager, and Tenant for full accountability
- **Prorated utility billing** — fair splitting based on actual occupancy days
- **Automated compliance** — scheduled tasks enforce legal deadlines and penalties

---

## CLOSING

That concludes the demonstration of Citispace Forerent. The system covers the **complete rental lifecycle** — from property setup and unit creation with ML-powered pricing, through tenant onboarding and contract signing, to monthly billing and payment verification, utility tracking, maintenance management, violation escalation, messaging, and finally the move-out process with automated deposit refund calculations.

The system is built on **three Philippine laws** — RA 9653, RA 8792, and RA 10173 — and two Civil Code articles to ensure legal compliance. It uses **machine learning** for intelligent pricing and forecasting, **real-time reactive interfaces** for a modern user experience, and **automated scheduled tasks** for consistent enforcement of business rules.

Every feature includes **validation** to prevent incorrect data entry, **role-based restrictions** to ensure users only access what they are authorized to see, **audit trails** for accountability, and **notification systems** to keep all parties informed.

Thank you. I am now ready to take your questions.

---

## APPENDIX: QUICK REFERENCE — SAD PATHS AND EDGE CASES

**(Keep this section for Q&A — do not read aloud unless asked)**

| Scenario                                     | What Happens                                                |
| -------------------------------------------- | ----------------------------------------------------------- |
| Wrong password at login                      | Error message shown, blocked after 5 attempts per minute    |
| Tenant tries to access /landlord URL         | 403 Forbidden — middleware blocks it                        |
| Login without accepting Terms                | Redirected to Terms page — must accept before proceeding    |
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
| Early termination move-out                   | Full deposit forfeited — refund is zero (Art. 1306)         |
| Concurrent ticket number generation          | Advisory database lock prevents duplicates                  |
| Late fee exceeds 25% of monthly rent         | Capped at 25% per Civil Code Article 1229                   |
| Password change without meeting requirements | Validation error — must include upper, lower, number, special |
| Dispute move-out inspection item             | Tenant can add remarks and evidence for disputed items      |
| Maintenance cost exceeds ₱10,000             | Visual warning alert shown to manager                       |
| Forecast API retry exhausted                 | Falls back after 3 attempts with exponential backoff        |

---

## APPENDIX: LEGAL REFERENCES AND BENCHMARKS

**(Use this section when the panel asks about the legal basis, references, or benchmarks of the system)**

---

### "What laws does your system follow?"

We follow **3 Republic Acts** and **2 Civil Code articles**.

**RA 9653 — Rent Control Act of 2009.** This is the main law for residential rental in the Philippines.
- **Section 6** — Max 1 month advance, max 2 months deposit. Our contracts enforce this.
- **IRR Section 7b** — Landlords must pay interest on deposits. Our system auto-computes daily interest and adds it to the refund.
- **IRR Section 7** — Refunds must be timely. We send automated reminders at 7 days, 3 days, and on the deadline.

**RA 8792 — Electronic Commerce Act of 2000.** Makes electronic signatures legally valid.
- Signatures are stored with SHA-256 hash, IP address, timestamp, and user agent.
- Signing follows a strict order: Owner, Manager, Tenant.
- A Contract Audit Log tracks every change for accountability.

**RA 10173 — Data Privacy Act of 2012.** Protects personal data.
- Encryption: AES-256 at rest, TLS/SSL in transit.
- Role-based access — users only see their own data.
- 72-hour breach notification to the National Privacy Commission.
- Data purged within 30 days of account deletion.

---

### "What Civil Code provisions do you reference?"

**Article 1306 — Liquidated Damages.** If a tenant terminates early, the deposit is forfeited in full. The contract states this, and the system enforces it automatically — refund goes to zero.

**Article 1229 — Equitable Reduction of Penalties.** Late fees are capped at 25% of monthly rent, no matter how many days overdue. This prevents excessive penalties.

---

### "What benchmarks did you use?"

**Security:** OWASP Top 10 — we use CSRF protection, bcrypt hashing, rate-limited login, role-based middleware, soft deletes, AES-256, and TLS/SSL.

**Machine Learning:** Agglomerative Clustering + per-cluster Linear Regression. Clustering groups similar units first, then regression predicts within each group — more accurate than a single global model. Falls back to defaults if the API is down.

**UX:** Livewire for real-time reactivity, multi-step wizards for complex forms, progressive disclosure on dashboards.

**Legal:** Three-party e-signature with audit trail, move-in vs. move-out inspection comparison for damage liability, prorated utility billing based on occupancy days.

---

### "How does your system ensure RA 9653 compliance?"

1. **Contracts** auto-include RA 9653 clauses for deposit and advance limits.
2. **Deposit interest** is auto-computed: deposit x annual rate / 365 x days held.
3. **Refund tracking** uses dedicated database fields for deadline, completion date, and reference number.
4. **Daily reminders** alert owners/managers at 7-day, 3-day, and overdue intervals.
5. **Renewal caps** — rate adjustments cannot exceed RA 9653 maximums.

---

### "Why these specific laws?"

- **RA 9653** — It is THE law for residential rental. Not optional.
- **RA 8792** — We use e-signatures. Without this, our contracts have no legal standing.
- **RA 10173** — We store personal data (names, IDs, financials). Compliance is mandatory.
- **Civil Code 1306 & 1229** — Legal basis for our penalty clauses. Makes them enforceable and fair.

---

### "What government agencies are relevant?"

1. **National Privacy Commission** — 72-hour breach notification required under RA 10173.
2. **Barangay Government** — Dispute resolution goes: negotiation, then Barangay mediation, then courts. We also require Barangay Clearance for property registration.

---

### Quick Reference Table

| Law / Article | What It Does | Where We Apply It |
|---|---|---|
| **RA 9653** | Rental terms, deposit limits, tenant protections | Contracts, deposit calc, refund reminders |
| **RA 9653 IRR §7b** | Interest on deposits | Auto-computed in Lease model |
| **RA 8792** | E-signatures are legal | SHA-256 hash, IP logging, audit trail |
| **RA 10173** | Data privacy | Encryption, access control, breach policy |
| **Art. 1306** | Liquidated damages | Early termination = deposit forfeited |
| **Art. 1229** | Penalty reduction | Late fee capped at 25% of rent |

---

## APPENDIX: DEMO ACCOUNTS

| Role      | Email                | Password |
| --------- | -------------------- | -------- |
| Landlord  | landlord@example.com | password |
| Manager   | manager@example.com  | password |
| Tenant    | tenant@example.com   | password |
| Manager 2 | manager2@example.com | password |
| Tenant 2  | tenant2@example.com  | password |
