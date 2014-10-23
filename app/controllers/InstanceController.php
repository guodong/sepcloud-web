<?php
use Rhumsaa\Uuid\Uuid;

class InstanceController extends BaseController
{

    public function index ()
    {
        $vms = Vm::all();
        return View::make('instance')->with('vms', $vms);
    }

    public function indexjson ()
    {
        $vms = Vm::orderBy("id")->get();
        foreach ($vms as &$v){
            $v->user;
        }
        return $vms->toJSON();
    }
    
    public function statusjson()
    {
        $uuid = $_GET['uuid'];
        $conn=libvirt_connect("null", false);
        try {
            $dm = libvirt_domain_lookup_by_uuid_string($conn, $uuid);
        } catch (Exception $e) {
            return json_encode(array('uuid'=>$uuid,'status'=>'0'));
        }
        $r = libvirt_domain_is_active($dm);
        return json_encode(array('uuid'=>$uuid,'status'=>$r));
    }
    
    public function start()
    {
        $id = $_GET['id'];
        $vm = Vm::find($id);
        $conn=libvirt_connect("null", false);
        libvirt_domain_create_xml($conn, $vm->xml);
    }
    
    public function shutdown()
    {
        $id = $_GET['id'];
        $vm = Vm::find($id);
        $conn=libvirt_connect("null", false);
        $dm = libvirt_domain_lookup_by_uuid_string($conn, $vm->uuid);
        libvirt_domain_shutdown($dm);
    }

    public function create ()
    {
        $uuid = Uuid::uuid1();
        $cmd = "qemu-img create -b /var/lib/libvirt/images/{$_POST['os']}.base.qcow2 -f qcow2 /var/lib/libvirt/images/{$uuid}.client.qcow2";
        system($cmd);
        sleep(1);
        $_POST['uuid'] = $uuid;
        $port = DB::table('vm')->max('spiceport');
        if (!$port)
            $port = 5900;
        $spiceport = ++$port;
        $_POST['spiceport'] = $spiceport;

        $mac = '52:54:'.implode(':',str_split(substr(md5(mt_rand()),0,8),2));
        $xml = <<<XML
<domain type='kvm'>
  <name>{$uuid}</name>
  <uuid>{$uuid}</uuid>
  <memory unit='MiB'>{$_POST['memory']}</memory>
  <currentMemory unit='MiB'>{$_POST['memory']}</currentMemory>
  <vcpu>{$_POST['cpu']}</vcpu>
  <os>
    <type arch='x86_64' machine='pc'>hvm</type>
    <boot dev='cdrom'/>
  </os>
  <features>
    <acpi/>
    <apic/>
    <pae/>
  </features>
  <clock offset='localtime'/>
  <on_poweroff>destroy</on_poweroff>
  <on_reboot>restart</on_reboot>
  <on_crash>restart</on_crash>
  <devices>
    <emulator>/usr/libexec/qemu-kvm</emulator>
    <disk type='file' device='disk'>
      <driver name='qemu' type='qcow2'/>
      <source file='/var/lib/libvirt/images/{$uuid}.client.qcow2'/>
      <target dev='hda' bus='virtio'/>
    </disk>
    <channel type='spicevmc'>
      <target type='virtio' name='com.redhat.spice.0'/>
      <address type='virtio-serial' controller='0' bus='0' port='1'/>
    </channel>
    <graphics type='spice' port='{$spiceport}' autoport='no' listen='0.0.0.0'/>
    <video>
      <model type='qxl' ram='65536' vram='65536' heads='1'/>
      <alias name='video0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
    </video>
    <sound model='ich6'>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x03' function='0x0'/>
    </sound>
    <interface type='bridge'>
      <mac address='{$mac}'/>
      <source bridge='br0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x04' function='0x0'/>
    </interface>
    <controller type='virtio-serial' index='0'>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x05' function='0x0'/>
    </controller>
  </devices>
</domain>
XML;
        $_POST['xml'] = $xml;
        $vm = Vm::create($_POST);
        $conn=libvirt_connect("null", false);
        libvirt_domain_create_xml($conn, $xml);
    }
}