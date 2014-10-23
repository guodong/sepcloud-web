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
        // $vm = Vm::create($_POST);
        $uuid = Uuid::uuid1();
        $cmd = "qemu-img create -b /var/lib/libvirt/images/{$_POST['os']}.base.qcow2 -f qcow2 /var/lib/libvirt/images/{$uuid}.qcow2";
        system($cmd);
        sleep(1);
        $_POST['uuid'] = $uuid;
        $port = DB::table('vm')->max('spiceport');
        if (!$port)
            $port = 5900;
        $spiceport = ++$port;
        $_POST['spiceport'] = $spiceport;
        $xml = <<<XML
<domain type='kvm'>
  <name>{$uuid}</name>
  <uuid>{$uuid}</uuid>
  <memory unit='MiB'>{$_POST['memory']}</memory>
  <currentMemory unit='MiB'>{$_POST['memory']}</currentMemory>
  <vcpu placement='static'>{$_POST['cpu']}</vcpu>
  <os>
    <type arch='x86_64' machine='pc-i440fx-rhel7.0.0'>hvm</type>
    <boot dev='hd'/>
  </os>
  <features>
    <acpi/>
    <apic/>
    <pae/>
  </features>
  <clock offset='utc'/>
  <on_poweroff>destroy</on_poweroff>
  <on_reboot>restart</on_reboot>
  <on_crash>restart</on_crash>
  <devices>
    <emulator>/usr/libexec/qemu-kvm</emulator>
    <disk type='file' device='disk'>
      <driver name='qemu' type='qcow2' cache='none'/>
      <source file='/var/lib/libvirt/images/{$uuid}.qcow2'/>
      <target dev='hda' bus='ide'/>
      <address type='drive' controller='0' bus='0' target='0' unit='0'/>
    </disk>
    <controller type='usb' index='0'>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x2'/>
    </controller>
    <controller type='pci' index='0' model='pci-root'/>
    <controller type='virtio-serial' index='0'>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x05' function='0x0'/>
    </controller>
    <interface type='bridge'>
      <mac address='52:54:00:e9:3a:8c'/>
      <source bridge='br0'/>
      <model type='rtl8139'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x07' function='0x0'/>
    </interface>
    <serial type='pty'>
      <target port='0'/>
    </serial>
    <console type='pty'>
      <target type='serial' port='0'/>
    </console>
    <channel type='spicevmc'>
      <target type='virtio' name='com.redhat.spice.0'/>
      <address type='virtio-serial' controller='0' bus='0' port='1'/>
    </channel>
    <input type='mouse' bus='ps2'/>
    <graphics type='spice' port='{$spiceport}' autoport='no' listen='0.0.0.0'>
      <listen type='address' address='0.0.0.0'/>
    </graphics>
    <sound model='ich6'>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x04' function='0x0'/>
    </sound>
    <video>
      <model type='qxl' ram='65536' vram='65536' heads='1'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
    </video>
    <memballoon model='virtio'>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x06' function='0x0'/>
    </memballoon>
  </devices>
</domain>
XML;
        $_POST['xml'] = $xml;
        $vm = Vm::create($_POST);
        $conn=libvirt_connect("null", false);
        libvirt_domain_create_xml($conn, $xml);
    }
}