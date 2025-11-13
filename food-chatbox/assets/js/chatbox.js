class FoodChatbot {
  constructor() {
    this.chatWindow = null;
    this.chatMessages = null;
    this.chatInput = null;
    this.sessionId = this.generateSessionId();
    this.userId = this.getUserId();
    this.init();
  }

  init() {
    this.createChatUI();
    this.attachEventListeners();
    this.sendWelcomeMessage();
  }

  generateSessionId() {
    return (
      "session_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9)
    );
  }

  getUserId() {
    const userIdElement = document.getElementById("chat-user-id");
    return userIdElement ? userIdElement.value : null;
  }

  createChatUI() {
    const chatHTML = `
            <div class="chatbox-container">
                <button class="chat-toggle-btn" id="chatToggleBtn">
                    <i class="fas fa-comments"></i>
                    <span class="chat-notification" id="chatNotification">1</span>
                </button>
                
                <div class="chat-window" id="chatWindow">
                    <div class="chat-header">
                        <div>
                            <h5><i class="fas fa-robot me-2"></i>Food Assistant</h5>
                            <small>Hỗ trợ đặt món ăn</small>
                        </div>
                        <button class="chat-close-btn" id="chatCloseBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="chat-messages" id="chatMessages">
                        <div class="typing-indicator" id="typingIndicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    
                    <div class="chat-input-container">
                        <form class="chat-input-form" id="chatForm">
                            <input type="text" 
                                   class="chat-input" 
                                   id="chatInput" 
                                   placeholder="Nhập 'món phở nào', 'tìm bún chả'..."
                                   autocomplete="off">
                            <button type="submit" class="chat-send-btn" id="chatSendBtn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;

    document.body.insertAdjacentHTML("beforeend", chatHTML);

    this.chatWindow = document.getElementById("chatWindow");
    this.chatMessages = document.getElementById("chatMessages");
    this.chatInput = document.getElementById("chatInput");
  }

  attachEventListeners() {
    const toggleBtn = document.getElementById("chatToggleBtn");
    const closeBtn = document.getElementById("chatCloseBtn");
    const chatForm = document.getElementById("chatForm");

    toggleBtn.addEventListener("click", () => this.toggleChat());
    closeBtn.addEventListener("click", () => this.closeChat());
    chatForm.addEventListener("submit", (e) => this.handleSubmit(e));
  }

  toggleChat() {
    this.chatWindow.classList.toggle("active");
    if (this.chatWindow.classList.contains("active")) {
      this.chatInput.focus();
      this.hideNotification();
    }
  }

  closeChat() {
    this.chatWindow.classList.remove("active");
  }

  showNotification() {
    const notification = document.getElementById("chatNotification");
    notification.classList.add("show");
  }

  hideNotification() {
    const notification = document.getElementById("chatNotification");
    notification.classList.remove("show");
  }

  async handleSubmit(e) {
    e.preventDefault();

    const message = this.chatInput.value.trim();
    if (!message) return;

    this.addMessage(message, "user");
    this.chatInput.value = "";

    this.showTyping();

    try {
      const response = await this.sendMessage(message);
      this.hideTyping();

      if (response.success) {
        // Tách tin nhắn thành nhiều phần nếu có nhiều món
        const messages = this.splitMessages(response.reply);
        console.log("Split messages:", messages); // Debug
        console.log("Total messages:", messages.length); // Debug

        messages.forEach((msg, index) => {
          setTimeout(() => {
            this.addMessage(msg, "bot");
          }, index * 500); // Delay 500ms giữa mỗi món để dễ nhìn hơn
        });
      } else {
        this.addMessage("Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.", "bot");
      }
    } catch (error) {
      this.hideTyping();
      this.addMessage("Không thể kết nối đến server. Vui lòng thử lại.", "bot");
    }
  }

  async sendMessage(message) {
    const response = await fetch("/foodonline/food-chatbox/src/api/chat.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        message: message,
        session_id: this.sessionId,
        user_id: this.userId,
      }),
    });

    return await response.json();
  }

  splitMessages(text) {
    // Tách tin nhắn thành nhiều cards riêng biệt dựa trên marker [DISH_ACTION:...]
    const messages = [];

    // Kiểm tra xem có DISH_ACTION không
    if (!text.includes("[DISH_ACTION:")) {
      return [text]; // Không phải food card, trả về nguyên
    }

    // Tách theo emoji 🍽️
    const parts = text.split("🍽️");

    // Phần đầu là intro (nếu có)
    if (parts[0] && parts[0].trim().length > 10) {
      messages.push(parts[0].trim());
    }

    // Mỗi phần còn lại là một món ăn
    for (let i = 1; i < parts.length; i++) {
      const dishText = "🍽️" + parts[i].trim();

      // Chỉ thêm nếu có DISH_ACTION marker
      if (dishText.includes("[DISH_ACTION:")) {
        // Cắt đến hết marker DISH_ACTION
        const endIndex = dishText.indexOf("[DISH_ACTION:");
        const markerEnd = dishText.indexOf("]", endIndex) + 1;

        if (markerEnd > 0) {
          messages.push(dishText.substring(0, markerEnd).trim());
        }
      }
    }

    // Thêm phần kết (nếu có text sau món cuối)
    const lastPart = parts[parts.length - 1];
    const lastMarkerIndex = lastPart.lastIndexOf("]");
    if (lastMarkerIndex > 0 && lastMarkerIndex < lastPart.length - 1) {
      const outro = lastPart.substring(lastMarkerIndex + 1).trim();
      if (outro.length > 10) {
        messages.push(outro);
      }
    }

    return messages.length > 0 ? messages : [text];
  }

  addMessage(text, sender) {
    const messageDiv = document.createElement("div");
    messageDiv.className = `chat-message ${sender}`;

    const avatar = document.createElement("div");
    avatar.className = `message-avatar ${sender}`;
    avatar.innerHTML =
      sender === "bot"
        ? '<i class="fas fa-robot"></i>'
        : '<i class="fas fa-user"></i>';

    const content = document.createElement("div");
    content.className = "message-content";

    const bubble = document.createElement("div");
    bubble.className = "message-bubble";

    // Parse action buttons từ text
    const parsedContent = this.parseActions(text);

    // Chuyển đổi line breaks thành <br>
    bubble.innerHTML = parsedContent.text.replace(/\n/g, "<br>");

    // Thêm border đẹp cho food card
    if (parsedContent.isFoodCard) {
      bubble.style.border = "2px solid #f0f0f0";
      bubble.style.borderRadius = "15px";
      bubble.style.padding = "15px";
      bubble.style.background = "linear-gradient(to bottom, #ffffff, #fafafa)";
    }

    const time = document.createElement("div");
    time.className = "message-time";
    time.textContent = new Date().toLocaleTimeString("vi-VN", {
      hour: "2-digit",
      minute: "2-digit",
    });

    content.appendChild(bubble);

    // Thêm action buttons ngay sau thông tin món
    if (parsedContent.actions.length > 0) {
      const actionsDiv = this.createActionButtons(parsedContent.actions);
      content.appendChild(actionsDiv);
    }

    content.appendChild(time);
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(content);

    const typingIndicator = document.getElementById("typingIndicator");
    this.chatMessages.insertBefore(messageDiv, typingIndicator);

    this.scrollToBottom();
  }

  parseActions(text) {
    const actions = [];
    let isFoodCard = false;

    // Kiểm tra xem có phải food card không
    if (text.includes("[DISH_ACTION:")) {
      isFoodCard = true;
    }

    // Tìm action cho từng món: [DISH_ACTION:food_id:restaurant_id:dish_name]
    const dishPattern = /\[DISH_ACTION:(\d+):(\d+):([^\]]+)\]/g;
    const restaurantPattern = /\[RESTAURANT:(\d+)\]/g;

    let match;

    while ((match = dishPattern.exec(text)) !== null) {
      actions.push({
        type: "dish",
        foodId: match[1],
        restaurantId: match[2],
        dishName: decodeURIComponent(match[3]),
      });
    }

    while ((match = restaurantPattern.exec(text)) !== null) {
      actions.push({
        type: "restaurant",
        restaurantId: match[1],
      });
    }

    // Loại bỏ tất cả markers
    let cleanText = text
      .replace(/\[DISH_ACTION:[^\]]+\]/g, "")
      .replace(/\[RESTAURANT:\d+\]/g, "")
      .replace(/\[FOOD_CARD_START\]/g, "")
      .replace(/\[FOOD_CARD_END\]/g, "")
      .replace(/\n{3,}/g, "\n\n")
      .trim();

    return {
      text: cleanText,
      actions: actions,
      isFoodCard: isFoodCard,
    };
  }

  createActionButtons(actions) {
    const actionsContainer = document.createElement("div");
    actionsContainer.className = "chat-actions";
    actionsContainer.style.cssText =
      "display: flex; gap: 8px; margin-top: 12px; margin-bottom: 5px;";

    actions.forEach((action) => {
      if (action.type === "dish") {
        // Nút thêm vào giỏ hàng
        const addToCartBtn = document.createElement("button");
        addToCartBtn.className = "chat-action-btn";
        addToCartBtn.innerHTML =
          '<i class="fas fa-shopping-cart"></i> Thêm giỏ';
        addToCartBtn.style.cssText = `
                    flex: 1;
                    padding: 10px 15px;
                    border-radius: 20px;
                    border: none;
                    background: linear-gradient(135deg, #fd4d40, #ff9b44);
                    color: white;
                    font-size: 13px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    box-shadow: 0 2px 8px rgba(253, 77, 64, 0.3);
                `;
        addToCartBtn.onmouseover = function () {
          this.style.transform = "translateY(-2px)";
          this.style.boxShadow = "0 4px 12px rgba(253, 77, 64, 0.5)";
        };
        addToCartBtn.onmouseout = function () {
          this.style.transform = "translateY(0)";
          this.style.boxShadow = "0 2px 8px rgba(253, 77, 64, 0.3)";
        };
        addToCartBtn.onclick = () =>
          this.addToCart(action.foodId, action.restaurantId);

        // Nút xem nhà hàng
        const viewRestBtn = document.createElement("button");
        viewRestBtn.className = "chat-action-btn";
        viewRestBtn.innerHTML = '<i class="fas fa-store"></i> Xem quán';
        viewRestBtn.style.cssText = `
                    flex: 1;
                    padding: 10px 15px;
                    border-radius: 20px;
                    border: 2px solid #fd4d40;
                    background: white;
                    color: #fd4d40;
                    font-size: 13px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                `;
        viewRestBtn.onmouseover = function () {
          this.style.background = "#fd4d40";
          this.style.color = "white";
          this.style.transform = "translateY(-2px)";
        };
        viewRestBtn.onmouseout = function () {
          this.style.background = "white";
          this.style.color = "#fd4d40";
          this.style.transform = "translateY(0)";
        };
        viewRestBtn.onclick = () => this.viewRestaurant(action.restaurantId);

        actionsContainer.appendChild(addToCartBtn);
        actionsContainer.appendChild(viewRestBtn);
      } else if (action.type === "restaurant") {
        const viewBtn = document.createElement("button");
        viewBtn.className = "chat-action-btn";
        viewBtn.innerHTML =
          '<i class="fas fa-external-link-alt"></i> Xem nhà hàng';
        viewBtn.style.cssText = `
                    width: 100%;
                    padding: 10px 15px;
                    border-radius: 20px;
                    border: none;
                    background: linear-gradient(135deg, #fd4d40, #ff9b44);
                    color: white;
                    font-size: 13px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                `;
        viewBtn.onclick = () => this.viewRestaurant(action.restaurantId);

        actionsContainer.appendChild(viewBtn);
      }
    });

    return actionsContainer;
  }

  addToCart(foodId, restaurantId) {
    // Thêm món vào giỏ hàng bằng form submit
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "dishes.php?res_id=" + restaurantId;
    form.style.display = "none";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "addtocart";
    input.value = foodId;

    const resInput = document.createElement("input");
    resInput.type = "hidden";
    resInput.name = "res_id";
    resInput.value = restaurantId;

    form.appendChild(input);
    form.appendChild(resInput);
    document.body.appendChild(form);
    form.submit();
  }

  viewRestaurant(restaurantId) {
    // Chuyển đến trang nhà hàng
    window.location.href = "dishes.php?res_id=" + restaurantId;
  }

  sendWelcomeMessage() {
    setTimeout(() => {
      this.addMessage(
        'Xin chào! 👋 Tôi là trợ lý ảo của DelishHub.\n\nBạn có thể hỏi:\n• "Món phở nào"\n• "Tìm bún chả"\n• "Đặt cơm gà"\n• "Món nào ngon"\n\nHãy thử hỏi tôi nhé! 😊',
        "bot"
      );
      this.showNotification();
    }, 1000);
  }

  showTyping() {
    document.getElementById("typingIndicator").style.display = "block";
    this.scrollToBottom();
  }

  hideTyping() {
    document.getElementById("typingIndicator").style.display = "none";
  }

  scrollToBottom() {
    this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
  }
}

// Initialize chatbot when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  new FoodChatbot();
});
