# Alsuwa ERP — Project Overview

## 1. Project Purpose

The Alsuwa ERP is an internal business management system designed to help Alsuwa manage its operations from acquiring new clients through sales, project delivery, and financial tracking.

The system will provide a central place to manage business information, coordinate departments, track work, and monitor business performance.

## 2. Business Objectives

The system aims to:

- Manage leads and convert qualified prospects into customers.
- Maintain centralized records of companies, contacts, and customer relationships.
- Manage sales opportunities, service proposals, quotations, and agreements.
- Plan and track projects from initiation to completion.
- Assign responsibilities to departments and individual staff members.
- Track invoices, payments, expenses, revenue, and project profitability.
- Measure marketing performance, lead conversion, sales performance, and overall business health.
- Provide role-appropriate access to information and functionality.

## 3. Intended Users

The system is initially intended for Alsuwa's internal team, including:

- Administrators
- Sales and business development staff
- Marketing staff
- Project managers
- Developers and other delivery team members
- Finance staff
- Management

Users will have permissions appropriate to their roles and responsibilities.

## 4. Core Business Areas

The system is expected to cover these areas:

1. **Identity and Access Management** — internal users, roles, and permissions.
2. **CRM** — leads, companies, contacts, customers, and relationship history.
3. **Sales Management** — deals, proposals, quotations, and sales outcomes.
4. **Project Management** — projects, assignments, tasks, milestones, and delivery status.
5. **Finance** — invoices, payments, expenses, revenue, and profitability.
6. **Marketing** — lead sources, campaigns, and marketing performance.
7. **Reporting and Analytics** — business performance and operational reports.
8. **Notifications and Integrations** — internal alerts and connections to external services where needed.

These areas will be implemented incrementally rather than all at once.

## 5. Core Business Principle

A lead represents a person or organization that may do business with Alsuwa.

A customer is a person or organization with whom Alsuwa has confirmed a working commitment.

A lead becomes a customer when that commitment is confirmed. An inquiry, meeting, or proposal alone does not automatically establish a customer relationship.

Existing customers can also be registered directly without first passing through the lead process.

The system should preserve the history of leads, sales opportunities, and customer relationships as they progress.

## 6. Technology Stack

The initial technology stack is:

- **Backend:** Laravel
- **Frontend:** Vue.js with Inertia.js
- **Authentication:** Laravel's Vue starter kit
- **Database:** PostgreSQL
- **Version control:** Git

Additional infrastructure and services will be introduced only when justified by the system's requirements.

## 7. Development Approach

The system will be developed incrementally.

For each business area, the team will document its requirements, business rules, workflows, data model, and acceptance criteria before implementing and testing the functionality.

The documentation will be maintained alongside the application code in the Git repository.

Database migrations and automated tests will be used to implement and verify the actual system behavior.

## 8. Initial Scope

The initial focus is to establish a reliable foundation for internal users and CRM functionality.

The first business functionality to be designed will cover:

- Companies and contacts
- Lead registration and tracking
- Lead qualification and follow-up
- Conversion from lead to customer
- Customer relationship history

Sales, project management, finance, marketing, and reporting will be developed in subsequent stages.

## 9. Scope Boundaries

This system is initially intended for Alsuwa's internal business operations.

A customer-facing portal, mobile application, advanced automation, and external integrations are not assumed to be part of the first release. They may be considered later when their business value and requirements are established.

## 10. Document Status

**Status:** Initial draft

This document establishes the high-level direction of the project. Detailed requirements, workflows, architecture, and database design will be documented separately and updated as decisions are confirmed.
