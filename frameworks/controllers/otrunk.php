<?php

// This controller handles most interactions with the Otrunk/OTML files

// TO DO, get the class settings for the accommodations and the student settings, merge them, then add the variables here.

// When DIY is run, it will reference this OTML file with parameters something like this:

// otrunk/member/run.otml

$otrunk_launcher = '<?xml version="1.0" encoding="UTF-8"?>
<otrunk id="0b21f3f0-c90b-11dc-95ff-0800200c9a66">
  <imports>
    <import class="org.concord.otrunk.OTInclude"/>
    <import class="org.concord.otrunk.OTIncludeRootObject"/>
    <import class="org.concord.otrunk.OTSystem"/>
    <import class="org.concord.otrunk.overlay.OTOverlay"/>
    <import class="org.concord.otrunk.script.OTScriptEngineBundle"/>
    <import class="org.concord.otrunk.script.OTScriptEngineEntry"/>
    <import class="org.concord.otrunk.udl.question.OTUDLQuestionViewConfig"/>
    <import class="org.concord.otrunk.view.document.OTCompoundDoc"/>
    <import class="org.concord.sensor.state.OTDeviceConfig"/>
    <import class="org.concord.sensor.state.OTInterfaceManager"/>
  </imports>
  <idMap>
  	<idMapping local_id="normal-text-overlay" id="6cc8e2b0-c44e-11dc-95ff-0800200c9a66"/>
	<idMapping local_id="large-text-overlay" id="7ba31210-c44e-11dc-95ff-0800200c9a66"/>
	<idMapping local_id="small-text-overlay" id="da41a2a0-c44e-11dc-95ff-0800200c9a66"/>
	<idMapping local_id="overlays-pulldown-menus" id="234e6400-c450-11dc-95ff-0800200c9a66"/>
	<idMapping local_id="imported-view-bundle" id="8d880970-c22a-11dc-95ff-0800200c9a66"/>
	<idMapping local_id="questions-card-view" id="c0e33320-e611-11dc-95ff-0800200c9a66"/>
  </idMap>
  <objects>
    <OTSystem local_id="system">
      <includes>
        <OTInclude href="global-imports/udl-view-bundle.otml"/>
      </includes>
      <overlays>
        <object refid="${normal-text-overlay}"/>
        <object refid="${size-top-menu-overlay}"/>
        <object refid="${question-config-overlay}"/>
      </overlays>
      <bundles>
        <object refid="${imported-view-bundle}"/>
        <OTScriptEngineBundle>
          <engines>
            <OTScriptEngineEntry objectClass="org.concord.otrunk.script.js.OTJavascript" engineClass="org.concord.otrunk.script.js.OTJavascriptEngine"/>
          </engines>
        </OTScriptEngineBundle>
        <OTInterfaceManager>
          <deviceConfigs>
            <OTDeviceConfig configString="none" deviceId="10"/>
          </deviceConfigs>
        </OTInterfaceManager>
      </bundles>
      <root>
        <OTIncludeRootObject href="udl-friction-56.otml"/>
      </root>
      <library>
      	<OTOverlay local_id="question-config-overlay">
      		<deltaObjectMap>
      			<entry key="c0e33320-e611-11dc-95ff-0800200c9a66">
      				<OTUDLQuestionViewConfig isLevelFrozen="false" defaultScaffoldLevel="0" objectClass="org.concord.otrunk.udl.question.OTUDLQuestions" viewClass="org.concord.otrunk.udl.question.OTUDLQuestionsCardView"/>
      			</entry>
      		</deltaObjectMap>
      	</OTOverlay>
      	<OTOverlay local_id="size-top-menu-overlay">
        	<deltaObjectMap>
        		<entry key="175ae3d0-e610-11dc-95ff-0800200c9a66">
        			<OTCompoundDoc name="top-menu" showEditBar="false">
			           <bodyText>
			             <div class="top-menu">
			               <table>
			                 <tr>
			                   <td>
			                     <object refid="${system}" viewid="textsize-choice-view"/>
			                   </td>
			                 </tr>
			               </table>
			             </div>
			           </bodyText>
			         </OTCompoundDoc>
        		</entry>
        	</deltaObjectMap>
        </OTOverlay>
      </library>
    </OTSystem>
  </objects>
</otrunk>
';

$display_headers = 'no';
header('Content-type: text/xml');
echo $otrunk_launcher;
exit;