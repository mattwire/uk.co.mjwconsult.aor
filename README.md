# uk.co.mjwconsult.aor
Customisations for AoR

## Quicksearch
Set External ID as default

## CPD Tutor
Customise display of custom group tab on contact summary.

## Address Listings
Hide FAR address custom data on event pages

## External ID (Membership Number)
This field holds the legacy AoR membership number.  For new contacts a new membership number is generated, starting at 30000.  Additionally this number is copied to the latest current membership (field custom_35).

## Contribution pages
When renewing memberships using contribution page 5, hide other membership types using contribution_form_5.js

## Membership
Hide non-priceset membership/renewal options on backend.

## Tokens
The following tokens are available:

| Event Tokens         | Description   |
| ------------- |-------------|
| event.type | Event Type |
| event.name | Event Name |
| event.membertickets | Number of member tickets |
| event.nonmembertickets | Number of non-member tickets |
| event.totaltickets | Total Number of tickets |
| event.membernetamount | Member net amount |
| event.membertaxamount | Member tax amount |
| event.membertotalamount | Member total amount |
| event.nonmembernetamount | Non Member net amount |
| event.nonmembertaxamount | Non Member tax amount |
| event.nonmembertotalamount | Non Member total amount |
| event.totalnetamount | Total Net Amount |
| event.totaltaxamount | Total Tax Amount |
| event.totalamount | Total Amount |

| Member Tokens         | Description   |
| ------------- |-------------|
| member.name | Membership Name |
| member.course_name | Membership Course Name |
| member.end_date | Membership End Date |
| member.start_date | Membership Start Date |
| member.join_date | Membership Join Date |
| member.qty | Membership Qty |
| member.totaltaxableamount | Membership Total Taxable Amount |
| member.totalnontaxableamount | Membership Total Non-Taxable Amount |
| member.totalnetamount | Membership Total Net Amount |
| member.totaltaxamount | Membership Total Tax Amount |
| member.totalamount | Membership Total Amount |
| member.lastnetamount | Membership (Last) Total Net Amount |
| member.lasttaxamount | Membership (Last) Total Tax Amount |
| member.lastamount | Membership (Last) Total Amount |

CPD Tokens are identical to member tokens, but only populated for CPD membership types.

| CPD Tokens         | Description   |
| ------------- |-------------|
| cpd.course_name | CPD Course Name |
| cpd.end_date | CPD End Date |
| cpd.start_date | CPD Start Date |
| cpd.join_date | CPD Join Date |
| cpd.qty | CPD Qty |
| cpd.totaltaxableamount | CPD Total Taxable Amount |
| cpd.totalnontaxableamount | CPD Total Non-Taxable Amount |
| cpd.totalnetamount | CPD Total Net Amount |
| cpd.totaltaxamount | CPD Total Tax Amount |
| cpd.totalamount | CPD Total Amount |
| cpd.lastnetamount | CPD (Last) Total Net Amount |
| cpd.lasttaxamount | CPD (Last) Total Tax Amount |
| cpd.lastamount | CPD (Last) Total Amount |

Advertiser Tokens are identical to member tokens, but only populated for Advertiser membership types.

| Advertiser Tokens         | Description   |
| ------------- |-------------|
| advertiser.course_name | Advertiser Course Name |
| advertiser.end_date | Advertiser End Date |
| advertiser.start_date | Advertiser Start Date |
| advertiser.join_date | Advertiser Join Date |
| advertiser.qty | Advertiser Qty |
| advertiser.totaltaxableamount | Advertiser Total Taxable Amount |
| advertiser.totalnontaxableamount | Advertiser Total Non-Taxable Amount |
| advertiser.totalnetamount | Advertiser Total Net Amount |
| advertiser.totaltaxamount | Advertiser Total Tax Amount |
| advertiser.totalamount | Advertiser Total Amount |
| advertiser.lastnetamount | Advertiser (Last) Total Net Amount |
| advertiser.lasttaxamount | Advertiser (Last) Total Tax Amount |
| advertiser.lastamount | Advertiser (Last) Total Amount |
