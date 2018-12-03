alter table masterkategori
add sitecode char(5);

CREATE VIEW [dbo].[vw_listtransfer]
AS
SELECT        TOP (100) PERCENT a.notrx, a.divcode, a.dtfppb, a.noteict, a.issend, a.isfullapproved, a.dtmodified, a.kategorifppb, b.div_id, b.div_nama, b.div_active, b.div_panjang, b.div_id_bias, c.idstatus, 
                         d.approvaltype, c.statustype, c.reason, c.dtfrom, c.dtthru, e.nik
FROM            dbo.tr_fppb_header AS a 
INNER JOIN dbMasterControl.dbo.master_division AS b ON a.divcode = b.div_id_bias 
INNER JOIN dbo.approvalstatus AS c ON a.notrx = c.notrx and c.approvaltype in (1,2,3,4,5,8)
INNER JOIN dbo.approvaltype AS d ON c.approvaltype = d.idapprovaltype
INNER JOIN dbo.masterkategori AS e ON a.kategorifppb = e.idkategori 
WHERE        (CURRENT_TIMESTAMP BETWEEN c.dtfrom AND c.dtthru)
and b.div_id like'djabesmen%';