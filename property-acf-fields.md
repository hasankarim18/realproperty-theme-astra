# Property ACF Field Reference

Site: **ReallProperty** — `http://realproperty.local`
Post type: `property` (REST base `property`)
ACF plugin: Advanced Custom Fields (field groups stored in the database, not local JSON)
Generated: 2026-11-15

**32 fields** across **5 field groups**. None are marked required. No field group has `show_in_rest` enabled, so these values are *not* available through the WordPress REST API — read/write them with `get_field()` / `update_field()`, or enable the ACF integration in Easy MCP AI.

## Field groups

| Group title | Group key | DB post ID | Fields |
|---|---|---|---|
| Property Information | `group_6ae698b793461` | 11 | 7 |
| Property Specifications | `group_6ae69d598f646` | 19 | 8 |
| Property Features | `group_6ae69e6564ef3` | 29 | 7 |
| Property Media | `group_6ae6c16600e01` | 46 | 9 |
| Property Agent | `group_6aedf09978c0e` | 264 | 1 |

All groups are located at: `post_type == property`.

---

## Property Information

| Field ID (key) | DB post ID | Field title | Field name | Type | Options / Return |
|---|---|---|---|---|---|
| `field_6ae698b8c2480` | 12 | Property Id | `property_id` | text | — |
| `field_6ae698fec2481` | 13 | Property Price | `property_price` | number | — |
| `field_6ae69991c2483` | 14 | Price Type | `price_type` | select | `fixed` → Fixed Price, `negotiable` → Negotiable (returns array) |
| `field_6ae69b41c2484` | 15 | Property Status | `property_status` | select | `for_sale` → For Sale, `for_rent` → For Rent, `sold` → Sold, `rented` → Rented (returns array) |
| `field_6ae69b87c2485` | 16 | Propety Address | `propety_address` | text | — (name misspelled in ACF, no double "r") |
| `field_6ae69b9bc2486` | 17 | Property Area | `property_area` | number | — |
| `field_6ae69bb9c2487` | 18 | Area Unit | `area_unit` | select | `sqft` → Sq Ft, `sqm` → Sq M, `acre` → Acre, `decimal` → Decimal (returns array) |

## Property Specifications

| Field ID (key) | DB post ID | Field title | Field name | Type | Options / Return |
|---|---|---|---|---|---|
| `field_6ae69d5959a08` | 20 | Bedrooms | `property_bedrooms` | number | — |
| `field_6ae69d8359a09` | 21 | Bathrooms | `property_bathrooms` | number | — |
| `field_6ae69d9859a0a` | 22 | Balconies | `balconies` | number | — |
| `field_6ae69dad59a0b` | 23 | Parking | `property_garage` | true_false | returns `1` / `0` |
| `field_6ae69db859a0c` | 24 | Total Floors | `total_floors` | number | — |
| `field_6ae69dcf59a0d` | 25 | Floor Number | `floor_number` | number | — |
| `field_6ae69deb59a0e` | 26 | Year Built | `year_built` | number | — |
| `field_6ae69df859a0f` | 27 | Furnishing | `property_furnishing` | select | `fully` → Fully Furnished, `semi` → Semi Furnished, `unfurnished` → Unfurnished (returns array) |

## Property Features

| Field ID (key) | DB post ID | Field title | Field name | Type | Options / Return |
|---|---|---|---|---|---|
| `field_6ae69e658fdc3` | 30 | Swimming Pool | `property_swimming_pool` | true_false | returns `1` / `0` |
| `field_6ae6a16588208` | 37 | Garden | `property_garden` | true_false | returns `1` / `0` |
| `field_6ae6bd03a0f68` | 39 | Gym | `property_gym` | true_false | returns `1` / `0` |
| `field_6ae6c0eff262e` | 41 | Security | `property_security` | true_false | returns `1` / `0` |
| `field_6ae6c104f262f` | 42 | Elevator | `property_elevator` | true_false | returns `1` / `0` |
| `field_6ae6c11df2630` | 43 | Air Conditioning | `property_ac` | true_false | returns `1` / `0` |
| `field_6ae6c130f2631` | 44 | Internet | `property_internet` | true_false | returns `1` / `0` |

## Property Media

| Field ID (key) | DB post ID | Field title | Field name | Type | Options / Return |
|---|---|---|---|---|---|
| `field_6ae6c1664e14b` | 47 | Property Image 1 | `property_image_1` | image | returns array (attachment) |
| `field_6ae6c2c081673` | 50 | Property Image 2 | `property_image_2` | image | returns array |
| `field_6ae6c2c181674` | 51 | Property Image 3 | `property_image_3` | image | returns array |
| `field_6ae6c2ce81675` | 52 | Property Image 4 | `property_image_4` | image | returns array |
| `field_6ae6c2d081676` | 53 | Property Image 5 | `property_image_5` | image | returns array |
| `field_6ae6c2d181677` | 54 | Property Image 6 | `property_image_6` | image | returns array |
| `field_6ae6c30f79ada` | 58 | Property Video | `property_video_url` | url | — |
| `field_6ae6c4ab79adb` | 59 | Floor Plan | `property_floor_plan` | image | returns array |
| `field_6ae6c4cd79adc` | 60 | Virtual Tour | `property_vertual_tour` | url | — (name misspelled in ACF) |

## Property Agent

| Field ID (key) | DB post ID | Field title | Field name | Type | Options / Return |
|---|---|---|---|---|---|
| `field_6aedf09ad2925` | 265 | Property Agent | `property_agent` | relationship | post type `agent`, returns array of post objects |

---

## Flat list (field key → title)

```
field_6ae698b8c2480  Property Id
field_6ae698fec2481  Property Price
field_6ae69991c2483  Price Type
field_6ae69b41c2484  Property Status
field_6ae69b87c2485  Propety Address
field_6ae69b9bc2486  Property Area
field_6ae69bb9c2487  Area Unit
field_6ae69d5959a08  Bedrooms
field_6ae69d8359a09  Bathrooms
field_6ae69d9859a0a  Balconies
field_6ae69dad59a0b  Parking
field_6ae69db859a0c  Total Floors
field_6ae69dcf59a0d  Floor Number
field_6ae69deb59a0e  Year Built
field_6ae69df859a0f  Furnishing
field_6ae69e658fdc3  Swimming Pool
field_6ae6a16588208  Garden
field_6ae6bd03a0f68  Gym
field_6ae6c0eff262e  Security
field_6ae6c104f262f  Elevator
field_6ae6c11df2630  Air Conditioning
field_6ae6c130f2631  Internet
field_6ae6c1664e14b  Property Image 1
field_6ae6c2c081673  Property Image 2
field_6ae6c2c181674  Property Image 3
field_6ae6c2ce81675  Property Image 4
field_6ae6c2d081676  Property Image 5
field_6ae6c2d181677  Property Image 6
field_6ae6c30f79ada  Property Video
field_6ae6c4ab79adb  Floor Plan
field_6ae6c4cd79adc  Virtual Tour
field_6aedf09ad2925  Property Agent
```

## Usage

```php
// Read
$price  = get_field('property_price', $post_id);      // by name
$price  = get_field('field_6ae698fec2481', $post_id); // by field key

// Write (key form is safest)
update_field('field_6ae698fec2481', 18500000, $post_id);

// Image fields accept an attachment ID
update_field('field_6ae6c1664e14b', $attachment_id, $post_id);

// Relationship accepts an array of post IDs
update_field('field_6aedf09ad2925', array(441, 443), $post_id);

// Select fields accept the choice value
update_field('field_6ae69991c2483', 'negotiable', $post_id);
```

Taxonomies attached to `property` (not ACF fields, but part of each listing):
`property_type` (Apartment, Duplex, House, Land, Office, Villa) and `property_location` (Banani, Bashundhara, Dhaka, Eskaton, Gulshan, Khulna, New Eskaton, Rajshahi, Sylhet).
