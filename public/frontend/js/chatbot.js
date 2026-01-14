// === Hybrid Chatbot: UI từ v1 + API Logic từ chatbot.js ===

// API Configuration từ chatbot.js
const API_KEY = "AIzaSyC7WfhTHwnFnmGJdf0PMwXc5S5edf7yanE"
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`

// System prompt cho chatbot văn phòng phẩm Sài Đồng
const SYSTEM_PROMPT = `Em là nhân viên tư vấn của cửa hàng văn phòng phẩm Sài Đồng. Em chỉ được phép trả lời các thông tin khái quát về cửa hàng, chính sách bán hàng, dịch vụ, quy trình mua hàng, đối tượng phục vụ, thông tin liên hệ, các chương trình khuyến mãi, hoặc các câu hỏi chung về lĩnh vực văn phòng phẩm.

LƯU Ý QUAN TRỌNG:
- Khi khách hỏi về thông tin sản phẩm cụ thể như giá, tồn kho, mô tả, giảm giá, trạng thái sản phẩm, em chỉ được phép trả lời các thông tin thực tế lấy từ hệ thống (database) qua API, tuyệt đối không tự bịa, không phỏng đoán, không trả lời nếu không có dữ liệu.
- Nếu không tìm thấy sản phẩm trong hệ thống, hãy trả lời lịch sự rằng em chưa có thông tin về sản phẩm đó và đề nghị khách kiểm tra lại tên hoặc hỏi sản phẩm khác.
- Không được giới thiệu sản phẩm của đối thủ, không trả lời các chủ đề không liên quan đến văn phòng phẩm.

Các thông tin khái quát em có thể tư vấn:
- Cửa hàng văn phòng phẩm Sài Đồng được thành lập năm 2010, chuyên cung cấp các sản phẩm văn phòng phẩm, dụng cụ học tập, thiết bị văn phòng, đồ dùng văn phòng chất lượng cao, giá cả hợp lý.
- Chính sách bảo hành, đổi trả rõ ràng, dịch vụ tận tâm, giao hàng nhanh, hỗ trợ khách hàng 24/7.
- Quy trình mua hàng: tư vấn, chọn sản phẩm, kiểm tra, thanh toán, giao hàng, hỗ trợ sau bán hàng.
- Đối tượng phục vụ: học sinh, sinh viên, giáo viên, công ty, tổ chức, cá nhân.
- Thông tin liên hệ: Địa chỉ 123 Đường Sài Đồng, Long Biên, Hà Nội. Hotline 0988 123 456. Email contact@saidong.com. Giờ làm việc 7:00 - 22:00 hàng ngày.
- Các chương trình khuyến mãi, ưu đãi, giảm giá theo từng thời điểm.

Nếu khách hỏi về sản phẩm cụ thể, hãy chuyển sang lấy dữ liệu thực tế từ hệ thống để trả lời đúng, ngắn gọn, trọng tâm, không tự bịa. Nếu không có dữ liệu, hãy xin lỗi lịch sự và đề nghị khách hỏi sản phẩm khác.

4. Lý do chọn văn phòng phẩm sài đồng:
- Giá cạnh tranh, sản phẩm đa dạng, giao hàng nhanh, hỗ trợ tận tình, cam kết chính hãng.

5. Chính sách bán hàng:
- Giao hàng nhanh nội thành Hà Nội, vận chuyển toàn quốc.
- Đổi trả lỗi sản phẩm trong 7 ngày.
- Ưu đãi lớn cho đơn sỉ và doanh nghiệp.

6. Quy trình đặt hàng:
- Đăng nhập/Đăng ký > Thêm sản phẩm > Nhập thông tin > Chọn thanh toán > Xác nhận đặt hàng.

7. Đối tượng khách hàng:
- Công ty, doanh nghiệp, trường học, cửa hàng nhỏ, cơ quan nhà nước, cá nhân.

8. Câu hỏi thường gặp:
- Có bán sỉ và lẻ không? → Có.
- Có giao hàng tận nơi không? → Có, nội thành và toàn quốc.
- Nếu sản phẩm không có trên web? → Liên hệ hotline hoặc email để tư vấn.
- Chính sách đổi trả? → Đổi trong 7 ngày nếu lỗi nhà sản xuất hoặc giao nhầm.

9. Thông tin liên hệ:
- Địa chỉ: 125 sài đồng, long biên, Hà Nội
- Hotline: 036 356 2320
- Email: nguyenky1588@gmail.com

10. Ưu đãi:
- Ưu đãi 100.000 đ cho đơn hàng đầu tiên với mã giảm giá.
- Mỗi lần mua tiếp theo có thể nhận thêm mã giảm giá dựa trên giá trị đơn hàng trước đó.

Ghi nhớ: Chỉ dựa vào thông tin trên để trả lời.`

// Chat history
const chatHistory = [
    { role: "user", parts: [{ text: SYSTEM_PROMPT }] },
    {
        role: "model",
        parts: [
            {
                text: "Chào anh/chị! Em là nhân viên tư vấn của cửa hàng văn phòng phẩm Sài Đồng. Anh/chị cần tư vấn về sản phẩm nào ạ?",
            },
        ],
    },
]

// Customer info and session management
let customerInfo = {
    name: "",
    email: "",
    phone: "",
    sessionId: "",
    isLoggedIn: false,
    isAdmin: false,
    role: "",
}

// Generate unique session ID
function generateSessionId() {
    return "session_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9)
}

// File data
const userData = {
    message: null,
    file: {
        data: null,
        mime_type: null,
    },
}

// Product analysis functions từ chatbot.js
function getQuestionType(msg) {
    msg = msg.toLowerCase()
    if (/giá|bao nhiêu|cost|price/.test(msg)) return "price"
    if (/tên|name/.test(msg)) return "name"
    if (/số lượng|tồn kho|còn bao nhiêu|stock|quantity/.test(msg)) return "stock"
    if (/mô tả|thông tin|description|summary|info/.test(msg)) return "summary"
    if (/giảm giá|discount/.test(msg)) return "discount"
    if (/còn bán|còn hàng|status|active|ngừng bán/.test(msg)) return "status"
    return "full"
}

function removeVietnameseTones(str) {
    str = str.normalize("NFD").replace(/\p{Diacritic}/gu, "")
    str = str.replace(/đ/g, "d").replace(/Đ/g, "D")
    return str
}

function getProductName(msg) {
    const raw = msg.toLowerCase()
    const noAccent = removeVietnameseTones(raw)

    const pricePatterns = [
        /giá ([^?.,]+)/i,
        /([^?.,]+) bao nhiêu/i,
        /([^?.,]+) giá bao nhiêu/i,
        /bao nhiêu tiền ([^?.,]+)/i,
        /([^?.,]+) bao nhiêu tiền/i,
        /cost of ([^?.,]+)/i,
        /price of ([^?.,]+)/i,
        /([^?.,]+) cost/i,
        /([^?.,]+) price/i,
    ]

    const generalPatterns = [
        /sản phẩm ([^?.,]+)/i,
        /cho tôi biết ([^?.,]+)/i,
        /tên ([^?.,]+)/i,
        /name ([^?.,]+)/i,
        /bút ([^?.,]+)/i,
        /vở ([^?.,]+)/i,
        /máy tính ([^?.,]+)/i,
        /tẩy ([^?.,]+)/i,
        /giấy ([^?.,]+)/i,
        /bìa ([^?.,]+)/i,
        /mực ([^?.,]+)/i,
        /ruột ([^?.,]+)/i,
        /gôm ([^?.,]+)/i,
        /chì ([^?.,]+)/i,
        /campus ([^?.,]+)/i,
        /casio ([^?.,]+)/i,
        /pentel ([^?.,]+)/i,
        /linc ([^?.,]+)/i,
        /hồng hà ([^?.,]+)/i,
        /pilot ([^?.,]+)/i,
    ]

    // Thử match với price patterns trước
    for (const p of pricePatterns) {
        const m = raw.match(p)
        if (m) {
            let productName = m[1].trim()
            productName = productName.replace(/\b(là|bao nhiêu|tiền|giá|của|sản phẩm|cho tôi biết)\b/gi, "").trim()
            if (productName) return productName
        }
        const m2 = noAccent.match(p)
        if (m2) {
            let productName = m2[1].trim()
            productName = productName.replace(/\b(la|bao nhieu|tien|gia|cua|san pham|cho toi biet)\b/gi, "").trim()
            if (productName) return productName
        }
    }

    // Thử match với general patterns
    for (const p of generalPatterns) {
        const m = raw.match(p)
        if (m) {
            let productName = m[1].trim()
            productName = productName.replace(/\b(là|bao nhiêu|tiền|giá|của|sản phẩm|cho tôi biết)\b/gi, "").trim()
            if (productName) return productName
        }
        const m2 = noAccent.match(p)
        if (m2) {
            let productName = m2[1].trim()
            productName = productName.replace(/\b(la|bao nhieu|tien|gia|cua|san pham|cho toi biet)\b/gi, "").trim()
            if (productName) return productName
        }
    }

    // Fallback
    const words = raw.split(" ")
    const stopWords = [
        "là",
        "bao",
        "nhiêu",
        "tiền",
        "giá",
        "của",
        "sản",
        "phẩm",
        "cho",
        "tôi",
        "biết",
        "tên",
        "name",
        "cost",
        "price",
        "the",
        "of",
        "và",
        "hoặc",
        "có",
        "không",
        "gì",
        "nào",
        "đó",
        "này",
        "kia",
    ]
    const filteredWords = words.filter((word) => !stopWords.includes(word))

    if (filteredWords.length >= 2) {
        return filteredWords.slice(-2).join(" ")
    } else if (words.length > 2) {
        return words.slice(-3).join(" ")
    }

    return null
}

// Gemini API call
const generateBotResponse = async (incomingMessageDiv, promptText) => {
    const messageElement = incomingMessageDiv.querySelector(".message-text")
    chatHistory.push({ role: "user", parts: [{ text: promptText }] })

    const requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ contents: chatHistory }),
    }

    try {
        const response = await fetch(API_URL, requestOptions)
        const data = await response.json()
        if (!response.ok) throw new Error(data.error.message)
        const apiResponseText = data.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g, "$1").trim()
        messageElement.innerText = apiResponseText
        chatHistory.push({ role: "model", parts: [{ text: apiResponseText }] })
        // Save bot message to database
        saveMessageToDatabase("bot", apiResponseText)
    } catch (error) {
        messageElement.innerText = error.message
        messageElement.style.color = "#ff0000"
    } finally {
        incomingMessageDiv.classList.remove("thinking")
        if (window.chatbot) {
            window.chatbot.scrollToBottom()
        }
    }
}

// Save message to database
async function saveMessageToDatabase(messageType, messageContent) {
    console.log("Attempting to save message:", { messageType, messageContent, customerInfo }) // Debug log

    if (!customerInfo.sessionId) {
        console.log("No session ID, skipping save")
        return
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content")
        console.log("CSRF Token:", csrfToken) // Debug log

        const requestData = {
            customer_name: customerInfo.name,
            customer_email: customerInfo.email,
            customer_phone: customerInfo.phone || "N/A", // Default for admin
            message_type: messageType,
            message_content: messageContent,
            session_id: customerInfo.sessionId,
        }

        console.log("Request data:", requestData) // Debug log

        const response = await fetch("/api/chat/save", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken || "",
                Accept: "application/json",
            },
            body: JSON.stringify(requestData),
        })

        console.log("Response status:", response.status) // Debug log

        const result = await response.json()
        console.log("Response data:", result) // Debug log

        if (!result.success) {
            console.error("Failed to save message:", result.message || result.errors)
        } else {
            console.log("Message saved successfully")
        }
    } catch (error) {
        console.error("Error saving message:", error)
    }
}

// ModernChatbot class từ v1 với logic API được tích hợp
class ModernChatbot {
    constructor() {
        this.isOpen = false
        this.messageCount = 0
        this.isTyping = false
        this.greetingSent = false

        // Check if user is logged in and their role
        this.checkAuthStatus()

        this.init()
    }

    checkAuthStatus() {
        // Check if user is logged in from window.authUser
        if (window.authUser && window.authUser.isLoggedIn) {
            customerInfo.isLoggedIn = true
            customerInfo.email = window.authUser.email
            customerInfo.isAdmin = window.authUser.isAdmin || false
            customerInfo.role = window.authUser.role || "user"
            console.log("User auth status:", window.authUser)
        } else {
            customerInfo.isLoggedIn = false
            customerInfo.isAdmin = false
            customerInfo.role = "guest"
            console.log("User is not logged in")
        }
    }

    init() {
        this.bindEvents()
        this.setupAutoScroll()
    }

    bindEvents() {
        // Toggle chatbot
        const toggleBtn = document.getElementById("chatbot-toggler")
        if (toggleBtn) {
            toggleBtn.addEventListener("click", () => {
                this.toggleChat()
            })
        }

        // Close chatbot
        const closeBtn = document.getElementById("close-chatbot")
        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
                this.closeChat()
            })
        }

        // Send message
        const sendBtn = document.getElementById("send-message")
        if (sendBtn) {
            sendBtn.addEventListener("click", () => {
                this.sendMessage()
            })
        }

        // Enter key to send
        const messageInput = document.getElementById("message-input")
        if (messageInput) {
            messageInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault()
                    this.sendMessage()
                }
            })

            // Input focus effects
            messageInput.addEventListener("focus", () => {
                const inputContainer = document.querySelector(".input-container")
                if (inputContainer) {
                    inputContainer.style.borderColor = "#4285f4"
                }
            })

            messageInput.addEventListener("blur", () => {
                const inputContainer = document.querySelector(".input-container")
                if (inputContainer) {
                    inputContainer.style.borderColor = "transparent"
                }
            })
        }

        // File upload
        const fileUploadBtn = document.querySelector("#file-upload")
        if (fileUploadBtn) {
            fileUploadBtn.addEventListener("click", () => {
                const fileInput = document.querySelector("#file-input")
                if (fileInput) {
                    fileInput.click()
                }
            })
        }

        // File input change
        const fileInput = document.querySelector("#file-input")
        if (fileInput) {
            fileInput.addEventListener("change", (e) => {
                const file = e.target.files[0]
                if (!file) return
                const reader = new FileReader()
                reader.onload = (e) => {
                    const base64String = e.target.result.split(",")[1]
                    userData.file = {
                        data: base64String,
                        mime_type: file.type,
                    }
                    e.target.value = ""
                }
                reader.readAsDataURL(file)
            })
        }

        // Emoji picker (simple)
        const emojiBtn = document.querySelector("#emoji-picker")
        if (emojiBtn) {
            emojiBtn.addEventListener("click", () => {
                const emojis = ["😊", "😂", "❤️", "👍", "👋", "🙏", "😍", "🎉", "💯", "🔥"]
                const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)]
                const messageInput = document.getElementById("message-input")
                if (messageInput) {
                    const { selectionStart: start, selectionEnd: end } = messageInput
                    messageInput.setRangeText(randomEmoji, start, end, "end")
                    messageInput.focus()
                }
            })
        }

        // Registration form submit - UPDATED FOR ADMIN USERS
        const customerForm = document.getElementById("customer-form")
        if (customerForm) {
            customerForm.addEventListener("submit", async (e) => {
                e.preventDefault()
                console.log("Form submitted via JavaScript") // Debug log

                const formData = new FormData(e.target)
                const name = formData.get("name")?.trim() || ""

                // Handle different user types
                let email = ""
                let phone = ""

                if (customerInfo.isAdmin) {
                    // Admin: only name required, use account email, set default phone
                    email = customerInfo.email
                    phone = "N/A" // Default for admin
                } else if (customerInfo.isLoggedIn) {
                    // Regular logged-in user: name + phone required, use account email
                    email = customerInfo.email
                    phone = formData.get("phone")?.trim() || ""
                } else {
                    // Guest: all fields required
                    email = formData.get("email")?.trim() || ""
                    phone = formData.get("phone")?.trim() || ""
                }

                // Clear previous errors
                document.getElementById("name-error").textContent = ""

                const phoneErrorElement = document.getElementById("phone-error")
                if (phoneErrorElement) {
                    phoneErrorElement.textContent = ""
                }

                const emailErrorElement = document.getElementById("email-error")
                if (emailErrorElement) {
                    emailErrorElement.textContent = ""
                }

                // Validate form based on user type
                let hasError = false

                if (!name) {
                    document.getElementById("name-error").textContent = "Vui lòng nhập tên"
                    hasError = true
                }

                // Only validate email for guests
                if (!customerInfo.isLoggedIn && !email) {
                    if (emailErrorElement) {
                        emailErrorElement.textContent = "Vui lòng nhập email"
                    }
                    hasError = true
                }

                // Only validate phone for non-admin users
                if (!customerInfo.isAdmin && !phone) {
                    if (phoneErrorElement) {
                        phoneErrorElement.textContent = "Vui lòng nhập số điện thoại"
                    }
                    hasError = true
                }

                if (hasError) return

                // Save customer info
                customerInfo = {
                    ...customerInfo, // Keep existing auth info
                    name: name,
                    email: email,
                    phone: phone,
                    sessionId: generateSessionId(),
                }

                console.log("Customer info saved:", customerInfo) // Debug log

                // Show loading
                const submitBtn = document.getElementById("submit-registration")
                const btnText = submitBtn.querySelector(".btn-text")
                const btnLoading = submitBtn.querySelector(".btn-loading")

                if (btnText) btnText.style.display = "none"
                if (btnLoading) btnLoading.style.display = "inline"
                submitBtn.disabled = true

                // Simulate loading delay
                setTimeout(() => {
                    // Hide registration form and show chat interface
                    const regForm = document.getElementById("registration-form")
                    const chatInterface = document.getElementById("chat-interface")

                    if (regForm) regForm.style.display = "none"
                    if (chatInterface) chatInterface.style.display = "flex"

                    // Show welcome message with role-based greeting
                    setTimeout(() => {
                        let welcomeMessage = ""
                        if (customerInfo.isAdmin) {
                            welcomeMessage = `Xin chào Admin ${customerInfo.name}! 👑 Chào mừng bạn quay trở lại hệ thống. Em là nhân viên tư vấn của cửa hàng văn phòng phẩm Sài Đồng. Anh/chị cần tư vấn về sản phẩm nào ạ?`
                        } else if (customerInfo.isLoggedIn) {
                            welcomeMessage = `Xin chào ${customerInfo.name}! 👋 Cảm ơn bạn đã đăng nhập. Em là nhân viên tư vấn của cửa hàng văn phòng phẩm Sài Đồng. Anh/chị cần tư vấn về sản phẩm nào ạ?`
                        } else {
                            welcomeMessage = `Xin chào ${customerInfo.name}! 👋 Em là nhân viên tư vấn của cửa hàng văn phòng phẩm Sài Đồng. Anh/chị cần tư vấn về sản phẩm nào ạ?`
                        }

                        chatbot.addMessage(welcomeMessage, "bot")
                    }, 500)

                    // Focus on message input
                    setTimeout(() => {
                        const messageInput = document.getElementById("message-input")
                        if (messageInput) {
                            messageInput.focus()
                        }
                    }, 800)

                    // Reset button state
                    if (btnText) btnText.style.display = "inline"
                    if (btnLoading) btnLoading.style.display = "none"
                    submitBtn.disabled = false

                    console.log("Switched to chat interface") // Debug log
                }, 1000)
            })
        } else {
            console.error("Customer form not found!") // Debug log
        }
    }

    toggleChat() {
        if (this.isOpen) {
            this.closeChat()
        } else {
            this.openChat()
        }
    }

    openChat() {
        const popup = document.getElementById("chatbot-popup")
        if (popup) {
            popup.style.display = "flex"
            this.isOpen = true

            // Scroll to bottom
            this.scrollToBottom()
        }
    }

    closeChat() {
        const popup = document.getElementById("chatbot-popup")
        if (popup) {
            popup.style.display = "none"
            this.isOpen = false
        }
    }

    async sendMessage() {
        const input = document.getElementById("message-input")
        if (!input) return

        const message = input.value.trim()
        if (!message) return

        // Add user message
        this.addMessage(message, "user")

        // Clear input
        input.value = ""

        // Show typing indicator
        this.showTypingIndicator()

        // Process message với logic từ chatbot.js
        await this.processMessage(message)
    }

    async processMessage(userMsg) {
        chatHistory.push({ role: "user", parts: [{ text: userMsg }] })

        const productName = getProductName(userMsg)
        let productInfoText = ""
        let prompt = SYSTEM_PROMPT
        const type = getQuestionType(userMsg)
        let shouldCallGemini = true

        if (productName) {
            try {
                const res = await fetch(`/api/product-info?name=${encodeURIComponent(productName)}`)
                if (res.ok) {
                    const data = await res.json()
                    switch (type) {
                        case "price":
                            productInfoText = `Giá sản phẩm ${data.name} là ${data.final_price}. Anh/chị cần tư vấn gì thêm không ạ?`
                            shouldCallGemini = false
                            break
                        case "name":
                            productInfoText = `Tên sản phẩm là ${data.name}. Anh/chị cần tư vấn gì thêm không ạ?`
                            shouldCallGemini = false
                            break
                        case "stock":
                            productInfoText = `Số lượng tồn kho của sản phẩm ${data.name} là ${data.stock}. Anh/chị cần tư vấn gì thêm không ạ?`
                            shouldCallGemini = false
                            break
                        case "summary":
                            productInfoText = `Mô tả sản phẩm ${data.name}: ${data.summary}. Anh/chị cần tư vấn gì thêm không ạ?`
                            shouldCallGemini = false
                            break
                        case "discount":
                            productInfoText = `Sản phẩm ${data.name} đang được giảm giá ${data.discount}%. Anh/chị cần tư vấn gì thêm không ạ?`
                            shouldCallGemini = false
                            break
                        case "status":
                            productInfoText = `Sản phẩm ${data.name} hiện ${data.is_active ? "còn bán" : "đã ngừng bán"}. Anh/chị cần tư vấn gì thêm không ạ?`
                            shouldCallGemini = false
                            break
                        default:
                            productInfoText = `Sản phẩm: ${data.name}, Giá gốc: ${data.price}, Giá sau giảm: ${data.final_price}, Tồn kho: ${data.stock}, Mô tả: ${data.summary}, Giảm giá: ${data.discount}%, Trạng thái: ${data.is_active ? "Còn bán" : "Ngừng bán"}.`
                            prompt = `${productInfoText}\n${SYSTEM_PROMPT}\nKhách hỏi: ${userMsg}`
                    }
                } else {
                    productInfoText = `Xin lỗi anh/chị, em chưa có thông tin về  "${productName}". Anh/chị có thể kiểm tra lại tên sản phẩm hoặc hỏi sản phẩm khác ạ.`
                    shouldCallGemini = false
                }
            } catch (error) {
                console.error("Error fetching product info:", error)
                productInfoText = `Xin lỗi anh/chị, hiện tại hệ thống đang gặp sự cố. Anh/chị vui lòng thử lại sau ạ.`
                shouldCallGemini = false
            }
        } else {
            prompt = `${SYSTEM_PROMPT}\nKhách hỏi: ${userMsg}`
        }

        setTimeout(() => {
            this.hideTypingIndicator()

            if (shouldCallGemini) {
                // Call Gemini API
                this.addBotMessageWithThinking(prompt)
            } else {
                // Direct response
                this.addMessage(productInfoText, "bot")
                chatHistory.push({ role: "model", parts: [{ text: productInfoText }] })
            }
        }, 600)
    }

    addBotMessageWithThinking(prompt) {
        const messagesContainer = document.getElementById("chat-messages")
        if (!messagesContainer) return

        const messageDiv = document.createElement("div")
        messageDiv.className = "message bot-message thinking"

        messageDiv.innerHTML = `
      <div class="message-avatar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
      </div>
      <div class="message-content">
        <div class="message-text">
          <div class="typing-dots">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
        </div>
      </div>
    `

        messagesContainer.appendChild(messageDiv)
        this.scrollToBottom()

        // Call Gemini API
        generateBotResponse(messageDiv, prompt)
    }

    addMessage(text, sender) {
        const messagesContainer = document.getElementById("chat-messages")
        if (!messagesContainer) return

        const messageDiv = document.createElement("div")
        const currentTime = new Date().toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
        })

        messageDiv.className = `message ${sender}-message new-message`

        if (sender === "user") {
            // Show admin crown for admin users
            const userIcon = customerInfo.isAdmin ? "👑" : "U"
            messageDiv.innerHTML = `
        <div class="message-content">
          <div class="message-text">${this.escapeHtml(text)}</div>
          <div class="message-time">${currentTime} <span class="message-status status-sent">✓</span></div>
        </div>

      `
        } else {
            messageDiv.innerHTML = `
        <div class="message-avatar">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
        <div class="message-content">
          <div class="message-text">${text}</div>
          <div class="message-time">${currentTime}</div>
        </div>
      `
        }

        messagesContainer.appendChild(messageDiv)
        this.scrollToBottom()
        this.messageCount++

        // Save message to database
        if (customerInfo.sessionId) {
            console.log("Calling saveMessageToDatabase for:", sender, text) // Debug log
            saveMessageToDatabase(sender, text)
        }
    }

    showTypingIndicator() {
        if (this.isTyping) return

        const messagesContainer = document.getElementById("chat-messages")
        if (!messagesContainer) return

        const typingDiv = document.createElement("div")
        typingDiv.className = "typing-indicator"
        typingDiv.id = "typing-indicator"

        typingDiv.innerHTML = `
      <div class="message-avatar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
      </div>
      <div class="typing-content">
        <div class="typing-dots">
          <div class="typing-dot"></div>
          <div class="typing-dot"></div>
          <div class="typing-dot"></div>
        </div>
      </div>
    `

        messagesContainer.appendChild(typingDiv)
        this.isTyping = true
        this.scrollToBottom()
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById("typing-indicator")
        if (typingIndicator) {
            typingIndicator.remove()
            this.isTyping = false
        }
    }

    setupAutoScroll() {
        const messagesContainer = document.getElementById("chat-messages")
        if (!messagesContainer) return

        const observer = new MutationObserver(() => {
            this.scrollToBottom()
        })

        observer.observe(messagesContainer, {
            childList: true,
            subtree: true,
        })
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById("chat-messages")
        if (messagesContainer) {
            setTimeout(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight
            }, 100)
        }
    }

    escapeHtml(text) {
        const div = document.createElement("div")
        div.textContent = text
        return div.innerHTML
    }
}

// Initialize chatbot when DOM is loaded
let chatbot
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM loaded, initializing chatbot...") // Debug log
    chatbot = new ModernChatbot()
    window.chatbot = chatbot // Make it globally accessible
})
