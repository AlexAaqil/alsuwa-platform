# Alsuwa ERP — Scope and Objectives

## 1. Purpose

This document defines the scope, objectives, priorities, and boundaries of the Alsuwa ERP.

It complements `00-project-overview.md` by explaining which capabilities the system should provide and how they will be introduced over time.

## 2. Project Objectives

The primary objectives are to:

1. Establish a single source of truth for Alsuwa's business information.
2. Manage prospects, leads, customers, and their business relationships.
3. Track sales opportunities from initial inquiry through a confirmed working commitment.
4. Manage projects and monitor their delivery progress.
5. Track business finances, including invoices, payments, expenses, and profitability.
6. Give management visibility into sales, operations, and financial performance.
7. Establish appropriate access controls, accountability, and auditability.
8. Reduce reliance on disconnected spreadsheets, manual follow-ups, and fragmented records.

## 3. Scope of the System

The ERP will cover the following business areas.

### 3.1 Identity and Access Management

- Internal user authentication.
- User profiles and account management.
- Roles and permissions.
- Access restrictions based on job responsibilities.

Authentication will initially use the Laravel Vue starter kit.

### 3.2 Customer Relationship Management (CRM)

- Register and manage leads.
- Record the source of each lead.
- Manage companies and individual contacts.
- Record inquiries, interactions, notes, and follow-up activities.
- Track lead qualification and status.
- Establish a customer relationship when a working commitment is confirmed.
- Maintain the history of lead and customer relationships.
- Identify existing records to reduce duplicate companies, contacts, and leads.

### 3.3 Sales Management

- Manage sales opportunities and deals.
- Record requested services and client requirements.
- Prepare and track proposals and quotations.
- Record approval, rejection, and negotiation outcomes.
- Track the transition from a successful deal to project delivery.

### 3.4 Project Management

- Create projects associated with customers and successful deals.
- Assign projects to responsible staff members.
- Track tasks, milestones, deadlines, and delivery status.
- Record project-related costs and delivery information.
- Monitor project progress and completion.

### 3.5 Finance

- Manage invoices and payments.
- Record business expenses and project costs.
- Track amounts billed, collected, outstanding, and spent.
- Calculate project revenue and profitability.
- Provide visibility into cash flow and financial performance.

Detailed accounting requirements will be established separately before implementing financial functionality.

### 3.6 Marketing

- Track lead sources and marketing channels.
- Record campaign information where applicable.
- Measure lead generation and conversion performance.
- Evaluate which channels generate useful business opportunities.

### 3.7 Reporting and Analytics

- Lead and customer reports.
- Sales pipeline and conversion reports.
- Project progress reports.
- Revenue, outstanding payments, and profitability reports.
- Marketing performance reports.
- Management dashboards.

Reports will be introduced as the underlying modules become available.

### 3.8 Notifications and Integrations

- Internal notifications and reminders.
- Follow-up reminders for sales and customer relationships.
- Integration with external services when there is a clear business requirement.

Specific providers and integration methods will be decided separately.

## 4. Delivery Priorities

Development will be incremental.

### Phase 1 — Foundation and CRM

- Configure the application foundation.
- Establish internal user roles and permissions.
- Design and implement companies and contacts.
- Implement lead registration and tracking.
- Implement lead qualification and follow-up.
- Implement the transition from lead to customer.
- Maintain relationship history and prevent avoidable duplicate records.

**Phase completion objective:** Alsuwa can manage prospects and established customers in one consistent system.

### Phase 2 — Sales Management

- Define the sales pipeline.
- Implement deals and sales stages.
- Manage proposals and quotations.
- Record sales outcomes.
- Link successful deals to customers and projects.

**Phase completion objective:** Alsuwa can track potential revenue from initial opportunity through a confirmed engagement.

### Phase 3 — Project Management

- Create and manage projects.
- Assign team members.
- Track tasks, milestones, and deadlines.
- Monitor project status and delivery.

**Phase completion objective:** Alsuwa can manage the delivery of work associated with successful sales.

### Phase 4 — Finance

- Implement invoicing and payment tracking.
- Record expenses and project costs.
- Track outstanding balances.
- Calculate project profitability and monitor cash flow.

**Phase completion objective:** Alsuwa can connect business engagements to the money billed, collected, and spent.

### Phase 5 — Marketing, Reporting, and Automation

- Improve marketing source and campaign tracking.
- Introduce cross-module dashboards.
- Automate appropriate reminders and repetitive workflows.
- Add integrations justified by actual operational needs.

**Phase completion objective:** Alsuwa can measure business performance and progressively automate routine operations.

These phases provide an initial roadmap. Detailed requirements and dependencies may change their order.

## 5. Exclusions from the Initial Release

The initial release will not automatically include:

- A customer-facing self-service portal.
- A separate mobile application.
- Full payroll and human resources management.
- Inventory and warehouse management.
- Advanced statutory accounting and tax automation.
- Complex workflow automation.
- Multiple-company or multi-tenant support.
- Extensive third-party integrations.

These are not permanent exclusions. They will be reconsidered if Alsuwa establishes a clear need for them.

## 6. Guiding Principles

### 6.1 Single Source of Truth

A company or person should not need multiple duplicate records merely because they interact with different departments.

### 6.2 Clear Business Concepts

Leads, contacts, companies, deals, customers, projects, and financial transactions must have distinct responsibilities.

### 6.3 Traceability

Important business events should preserve enough history to understand what happened, when it happened, and who was responsible.

### 6.4 Incremental Development

Each phase should deliver useful, testable functionality before unnecessary complexity is introduced.

### 6.5 Security by Design

Users should only access information and perform actions permitted by their responsibilities.

### 6.6 Financial Accuracy

Financial records must be consistent, traceable, and designed to support reliable reporting. Financial requirements will be reviewed carefully before implementation.

## 7. Initial Success Criteria

The first release should enable Alsuwa to:

- Register and track new leads.
- Identify the source of each lead.
- Record interactions and follow-up activities.
- Manage companies and their associated contacts.
- Convert a qualified lead into a customer when a working commitment is confirmed.
- Register an existing customer directly.
- Preserve relationship history through the transition.
- Restrict functionality according to staff permissions.
- Retrieve customer and lead information without relying on separate spreadsheets.

## 8. Dependencies and Open Decisions

The following details require further definition before their respective features are implemented:

- The exact lead qualification and status rules.
- The required company and contact fields.
- The conditions and records needed to confirm a working commitment.
- The sales pipeline and deal stages.
- The quotation and proposal approval process.
- Project cost allocation and profitability calculations.
- Financial reporting and accounting requirements.
- The role and permission matrix.

These decisions will be documented in the relevant requirements, workflow, and system design documents.

## 9. Document Status

**Status:** Initial draft

This document defines the intended scope and delivery priorities. Detailed behavior, workflows, database structures, and acceptance criteria will be maintained in their respective documents.
