# Roles and Permissions User Guide

## Table of Contents
1. [What Are Roles and Permissions?](#what-are-roles-and-permissions)
2. [How the System Works](#how-the-system-works)
3. [Types of Permissions](#types-of-permissions)
4. [Managing Roles](#managing-roles)
5. [Common Scenarios](#common-scenarios)

---

## What Are Roles and Permissions?

Think of **roles** like job titles in your parish that come with specific responsibilities:

- **Roles** are like job descriptions - each role defines what someone in that position can do
- **Permissions** are the individual tasks that make up each role (like "view member information" or "update group lists")

Just as you might hire someone as a "Catechist" (which automatically means they can teach classes, access student records, and contact families), you assign roles to system users which automatically give them all the capabilities they need for their job.

The system keeps this simple: **you only assign roles to people**. The system administrators have already set up each role with the right permissions, so you don't need to worry about the technical details.

---

## How the System Works

The system checks permissions every time someone tries to do something:

1. **User attempts an action** (like viewing member information)
2. **System checks**: "Does this user have permission to view member information?"
3. **System allows or denies** the action based on their permissions

This happens automatically - users don't see these checks, they simply can or cannot access certain features.

---

## Types of Permissions

### Standard Data Management Permissions
For each type of information (users, households, members, etc.), there are seven standard permissions:

- **View Any**: See the list of all records
- **View**: See details of individual records
- **Create**: Add new records
- **Update**: Modify existing records
- **Delete**: Remove records (usually moves to trash)
- **Restore**: Bring back deleted records from trash
- **Force Delete**: Permanently remove records

### Special Activity Permissions
Some permissions control specific activities:

#### Member Management
- **View Groups**: See which groups a member belongs to
- **Add to Group**: Put members into groups
- **Remove from Group**: Take members out of groups

#### Group Management
- **View Members**: See who belongs to a group
- **Add Member**: Put new people into the group
- **Remove Member**: Take people out of the group

#### Sacramental Records
For each sacrament (Baptism, First Communion, Confirmation), there are specific permissions:
- **View Date**: See when the sacrament was received
- **View Parish**: See where the sacrament was received
- **View Certificate**: See certificate information
- **Download Certificate**: Download certificate files

---

## Managing Roles

### Assigning Roles
When you give someone a role, they automatically receive all the permissions that come with that role. This is the only way users are given access - **there are no individual permissions to manage**.

### Available Roles
System administrators create and manage the available roles (like "Catechist", "Group Leader", "Office Assistant"). You simply choose which existing role best fits each person's job.

### Multiple Roles
A person can have multiple roles if their job spans different areas. For example, someone might be both a "Catechist" and a "Group Leader".

### Group Leadership
Some users can be designated as group leaders. Group leaders automatically have certain permissions for the groups they lead, even if their role doesn't normally include those permissions.

---

## Common Scenarios

### Scenario 1: New Volunteer
**Situation**: A new volunteer joins and will help with children's programs.

**Solution**: Assign them the "Catechist" role, which automatically gives them permission to:
- View and update member information for children in their groups
- View sacramental information
- Manage group membership for their assigned groups

### Scenario 2: Office Assistant
**Situation**: Someone helps in the parish office part-time and needs to enter new families.

**Solution**: Assign them the "Office Assistant" role, which includes permissions to:
- Create new households and members
- Update basic information
- But not delete records or access sensitive information

### Scenario 3: Temporary Access
**Situation**: During preparation for First Communion, extra volunteers need to access certificates.

**Solution**: Temporarily assign them a role that includes certificate access:
- Give them the "Sacrament Coordinator" role during the preparation period
- Remove the role after the event is complete

### Scenario 4: Department Head
**Situation**: Someone oversees multiple programs and needs broader access.

**Solution**: Assign them multiple roles that cover their responsibilities, such as:
- "Catechist" role for educational programs
- "Group Leader" role for managing groups
- "Sacrament Coordinator" role for sacramental records

---

## Key Principles to Remember

1. **Roles Only**: You only assign roles to users - the system takes care of all the detailed permissions automatically.

2. **Choose the Right Role**: Select the role that best matches the person's actual job responsibilities.

3. **Multiple Roles When Needed**: If someone has responsibilities that span different areas, give them multiple roles rather than trying to find one perfect role.

4. **Regular Review**: Periodically review who has what roles, especially when people change positions.

5. **Remove Unused Roles**: When someone's responsibilities change, remove roles they no longer need.

6. **Keep It Simple**: The system is designed to be straightforward - don't overthink role assignments.

---

## Getting Help

If you're unsure about what role someone should have:
- Look at what similar people in your organization have been assigned
- Consider what specific tasks they need to accomplish in their job
- Start with a basic role and add additional roles as needed
- Ask your system administrator if you need a new role created

The system is designed to be flexible - you can always modify someone's roles as their responsibilities change or evolve.