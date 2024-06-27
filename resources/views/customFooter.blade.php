<div style="margin-bottom: 100px;">
    <div class="text-center mb-4 md:mb-0">
        Developed @
        <a href="#" class="text-blue-500 hover:text-blue-700 font-bold">ICT LPPSA</a>

    </div>



</div>

<footer class="flex justify-between fixed bottom-0 left-0 z-20 w-full p-4 bg-white border-t border-gray-200 shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-gray-800 dark:border-gray-600">
    <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }}
        <a href="#" class="hover:underline">ICT LPPSA Site™</a>. All Rights Reserved.
    </span>
    <div class="flex">

        <img alt="lppsa logo" src="{{ url('assets/logo.png') }}" style="width: 50px;" class="fi-logo justify-self-end p-1">
    </div>
</footer>



<script>
  
        async function callbackFunction() {

            const selectedEle = document.querySelector("#copylinkurl");
            console.log("selectedEle", selectedEle)
            if (selectedEle) {
                let linkToCopy = selectedEle.innerHTML;
                console.log("linkToCopy", linkToCopy)
                new FilamentNotification()
                    .title("Link URL Copied")
                    .success()
                    .body("Copy Successfull")
                    .send();

                try {
                    await copyToClipboard(linkToCopy);
                } catch (error) {
                    console.error(error);
                }

            }

        }
        document?.querySelector("#copylink")?.removeEventListener("click", callbackFunction)
        document?.querySelector("#copylink")?.addEventListener("click", callbackFunction)


        async function copyToClipboard(textToCopy) {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(textToCopy);
            } else {
                const textArea = document.createElement("textarea");
                textArea.value = textToCopy;

                textArea.style.position = "absolute";
                textArea.style.left = "-999999px";

                document.body.prepend(textArea);
                textArea.select();

                try {
                    document.execCommand("copy");
                } catch (error) {
                    console.error(error);
                } finally {
                    textArea.remove();
                }
            }
        }

   
</script>

