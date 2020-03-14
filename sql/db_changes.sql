/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pratiraj
 * Created: 16 Aug, 2018
 */

insert into it_colors set color='NA';
alter table it_polines add column ctg_id bigint(20) not null after product_id;

CREATE TABLE it_transportation (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  name varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  phoneno varchar(20) DEFAULT NULL,
  inactive tinyint(1) NOT NULL DEFAULT '0',
  createtime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatetime timestamp NULL DEFAULT NULL,
  created_by bigint(20) DEFAULT NULL,
  updated_by bigint(20) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name (name),
  KEY created_by (created_by),
  KEY updated_by (updated_by),
  KEY inactive (inactive)
);

CREATE TABLE it_gst_percentage (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  tax_name varchar(20) DEFAULT NULL,
  value double NOT NULL DEFAULT 0,
  rate double DEFAULT NULL,
  createdby bigint(20) DEFAULT NULL,
  createtime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedby bigint(20) DEFAULT NULL,
  updatetime timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
);

insert into it_gst_percentage set tax_name='GST 5%',value=5,rate=0.05,createdby=2;
insert into it_gst_percentage set tax_name='GST 12%',value=12,rate=0.12,createdby=2;
insert into it_gst_percentage set tax_name='GST 18%',value=18,rate=0.18,createdby=2;
insert into it_gst_percentage set tax_name='GST 28%',value=28,rate=0.28,createdby=2;

alter table it_purchaseorder add column freightamt double default 0 after referance2;
alter table it_purchaseorder add column freight_gst double default 0 after freightamt;
alter table it_purchaseorder add column transport_id int(11) default null  after freight_gst;
alter table it_purchaseorder add column freight_total double default 0 after transport_id;

alter table it_purchaseorder add column is_mailsent tinyint(1) default 0 after approvedby ;

alter table it_purchaseorder add column delivery_note text default null after remarks;
 alter table it_purchaseorder add column header_note text default null after delivery_note;
alter table it_purchaseorder add column remark_note text default null after remarks;

alter table it_purchaseorder add column cancelreason text default null after remarks;